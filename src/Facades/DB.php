<?php
namespace PatrykNamyslak\Patbase\Facades;

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

final class DB{
    use Core;
    /**
     * The patbase instance that is used for DB calls
     */
    private static Patbase $db;
    private static Query $queryBuilder;

    public static function configure(Config $configuration){
        self::$config = $configuration;
    }

    /**
     * Maps the environment variables to the stored config
     * @param string $envFileDirectory For example `"/srv/websites/my_site/"` (do not include .env)
     * * If it is left as `NULL` it will default to the `DOCUMENT_ROOT` stored in `$_SERVER`.
     * @return Config
     */
    public static function configureFromEnv(?string $envFileDirectory = NULL): void{
        $config = new Config();
        self::loadEnv($envFileDirectory ?? $config->envFileDirectory);
        $config->host = $_ENV[EnvironmentVariable::HOST->value];
        $config->database = $_ENV[EnvironmentVariable::DATABASE->value];
        $config->driverType($_ENV[EnvironmentVariable::DRIVER->value]);
        $config->fetchMode(mode: $_ENV[EnvironmentVariable::FETCH_MODE->value]);
        $config->username = $_ENV[EnvironmentVariable::USERNAME->value];
        $config->password = $_ENV[EnvironmentVariable::PASSWORD->value];
        // autoConnect is true when lazy load is false in the .env
        $config->autoConnect = !(filter_var($_ENV[EnvironmentVariable::LAZY_LOAD->value], FILTER_VALIDATE_BOOLEAN));
        self::configure($config);
    }

    public static function connect(): void{
        self::$db = Patbase::constructWithConfig(config: self::$config);
        self::$db->connect();
    }
    public static function select(string|array $columns = ["*"]): SelectQuery{
        $columns = match(true){
            $columns === [], empty($columns) => ["*"],
            is_string($columns) => array($columns),
            default => $columns,
        };
        self::$queryBuilder = new SelectQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function insert(array $columns): InsertQuery{
        self::$queryBuilder = new InsertQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function update(array $columns): UpdateQuery{
        self::$queryBuilder = new UpdateQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function delete(): DeleteQuery{
        self::$queryBuilder = new DeleteQuery(db: self::$db);
        return self::$queryBuilder;
    }
    public static function insertOrUpdate(array $columns): InsertOrUpdateQuery{
        self::$queryBuilder = new InsertOrUpdateQuery(db: self::$db, columns: $columns);
        return self::$queryBuilder;
    }
    public static function upsert(array $columns): InsertOrUpdateQuery{
        return self::insertOrUpdate($columns);
    }

}