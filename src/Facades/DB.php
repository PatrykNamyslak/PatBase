<?php
namespace PatrykNamyslak\Patbase\Facades;

use Dotenv\Dotenv;
use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Builders\DeleteQuery;
use PatrykNamyslak\Patbase\Builders\InsertOrUpdateQuery;
use PatrykNamyslak\Patbase\Builders\InsertQuery;
use PatrykNamyslak\Patbase\Builders\SelectQuery;
use PatrykNamyslak\Patbase\Builders\UpdateQuery;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;
use PatrykNamyslak\Patbase\Enums\QueryType;
use PatrykNamyslak\Patbase\Enums\WhereOperator;
use PatrykNamyslak\Patbase\Support\Config;
use PatrykNamyslak\Patbase\Traits\Core;
use PatrykNamyslak\Patbase\Enums\EnvironmentVariable;


use PatrykNamyslak\Patbase\Blueprints\Query;
use RuntimeException;

class DB{
    use Core;
    /**
     * The patbase instance that is used for DB calls
     */
    private static ?Patbase $db = NULL;
    private static Query $queryBuilder;

    public static function configure(Config $configuration){
        self::$config = $configuration;
    }

    /**
     * Maps the environment variables to the stored config
     * @param Dotenv Pass in a valid env loader by using `Dotenv\Dotenv::createImmutable()` or any method that returns an instance of `Dotenv\Dotenv` Read more at: https://github.com/vlucas/phpdotenv.
     * @return Config
     */
    public static function configureFromEnv(Dotenv $envLoader): void{
        self::loadEnv(envLoader: $envLoader);
        $config = new Config(
            autoLoad: true,
            driverType: DatabaseDriver::tryFrom($_ENV[EnvironmentVariable::DRIVER->value]) ?? DatabaseDriver::MYSQL,
            host: $_ENV[EnvironmentVariable::HOST->value],
            username: $_ENV[EnvironmentVariable::USERNAME->value],
            password: $_ENV[EnvironmentVariable::PASSWORD->value],
            database: $_ENV[EnvironmentVariable::DATABASE->value],
            port: $_ENV[EnvironmentVariable::PORT->value],
            fetchMode: Config::fetchMode($_ENV[EnvironmentVariable::FETCH_MODE->value]),
            lazyLoad: filter_var($_ENV[EnvironmentVariable::LAZY_LOAD->value], FILTER_VALIDATE_BOOLEAN),
        );
        // autoConnect is true when lazy load is false in the .env
        self::configure($config);
    }

    public static function connect(): void{
        self::$db = Patbase::constructWithConfig(config: self::$config);
        self::$db->connect();
    }
    public static function select(string|array $columns = ["*"]): SelectQuery{
        if (!self::$db && self::$config->lazyLoad){
            self::connect();
        }
        $columns = match(true){
            $columns === [], empty($columns) => ["*"],
            is_string($columns) => array($columns),
            default => $columns,
        };
        self::$queryBuilder = new SelectQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function insert(array $columns): InsertQuery{
        if (!self::$db && self::$config->lazyLoad){
            self::connect();
        }
        self::$queryBuilder = new InsertQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function update(array $columns): UpdateQuery{
        if (!self::$db && self::$config->lazyLoad){
            self::connect();
        }
        self::$queryBuilder = new UpdateQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function delete(): DeleteQuery{
        if (!self::$db && self::$config->lazyLoad){
            self::connect();
        }
        self::$queryBuilder = new DeleteQuery(db: self::$db);
        return self::$queryBuilder;
    }
    public static function insertOrUpdate(array $columns): InsertOrUpdateQuery{
        if (!self::$db && self::$config->lazyLoad){
            self::connect();
        }
        self::$queryBuilder = new InsertOrUpdateQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }

    /**
     * Build an `Insert or Update` query
     * @param string[] $columns
     * @return Patbase\Builders\InsertOrUpdateQuery
     */
    public static function upsert(array $columns): InsertOrUpdateQuery{
        return self::insertOrUpdate($columns);
    }

}