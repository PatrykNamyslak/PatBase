<?php
namespace PatrykNamyslak;

use Exception;
use InvalidArgumentException;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;
use PatrykNamyslak\Patbase\Query;
use PatrykNamyslak\Patbase\Support\Config;
use PatrykNamyslak\Patbase\Traits\Core;
use PDOException;
use UnexpectedValueException;
/**
 * * Datbase abstraction layer for managing database connections using PDO
 */
class Patbase {
    use Core;
    protected(set) ?\PDO $connection = NULL;
    public string $dsn;

    /**
     * Summary of __construct
     * @param DatabaseDriver $driverType
     * @param string $host
     * @param string $username Required for all database drivers except SQL Lite
     * @param string $password
     * @param string $database
     * @param int|string|null $port Required if your database uses a different port than the defaults defined in `Patbase::getDefaultPort()`
     * @param int $fetchMode
     * @param bool $autoConnect Choose to whether connect on object instantation (connect in the constructor)
     * @param array|null $options Extra configuration options for PDO i.e: `[PDO::ATTR_CASE, PDO::CASE_LOWER]` Read more at: https://www.php.net/manual/en/pdo.setattribute.php
     */
    public function __construct(
        protected DatabaseDriver $driverType = DatabaseDriver::MYSQL, 
        protected string $host, 
        protected ?string $username, 
        protected ?string $password, 
        protected string $database,
        protected int|string|null $port = NULL,
        public int $fetchMode = \PDO::FETCH_ASSOC, 
        bool $autoConnect = true,
        protected ?array $options = NULL,
        ){
            // Just a little heads up in case you choose the wrong driver by accident
            if ($driverType === DatabaseDriver::SQL_LITE and ($username or $password)){
                trigger_error("If you are using SQL lite you do not need a username or password!", E_WARNING);
            }
            $this->port = match($this->port){
                null => $this->getDefaultPort(),
                default => $this->port,
            };
            $this->dsn = $this->generateDsn();
            // Lazy load the connection
            if ($autoConnect){
                $this->connect();
            }
    }

    public static function constructWithConfig(Config $config): Patbase{
        return self::handleObjectConfig($config);
    }

    private static function handleObjectConfig(Config $config): Patbase{
        return new self(
            driverType: $config->driverType,
            host: $config->host,
            username: $config->username,
            password: $config->password,
            database: $config->database,
            port: $config->port,
            fetchMode: $config->fetchMode,
            autoConnect: $config->autoConnect,
            options: $config->options,
        );
    }
    /**
     * Use this if you want to use an array $config
     * @param array $config
     * @throws InvalidArgumentException
     * @return Patbase
     * @deprecated This configuration option is no longer supported / The library relies on the `PatrykNamyslak\Patbase\Support\Config::class` provided
     */
    private static function handleArrayConfig(array $config): Patbase{
        return new self(
            $config["driver"],
            $config["host"],
            $config["username"] ?? $config["user"],
            $config["password"] ?? $config["pass"],
            $config["database"],
            $config["port"] ?? NULL,
            $config["fetch_mode"] ?? $config["fetchMode"],
            // The exclamation mark is to do the opposite, so if lazyLoad is on (true) then autoconnect = false (off).
            $config["auto_connect"] ?? $config["autoConnect"] ?? !$config["lazyLoad"] ?? !$config["lazy_load"],
            $config["options"],
        );
    }

    protected function getDefaultPort(): int|null{
        return match($this->driverType){
            DatabaseDriver::SQL_LITE => NULL,
            DatabaseDriver::MYSQL => 3306,
            DatabaseDriver::POSTGRES => 5432,
            DatabaseDriver::MS_SQL_SERVER, DatabaseDriver::MS_SQL_SERVER_LINUX => 1433,
            DatabaseDriver::ORACLE => 1521,
            DatabaseDriver::FIREBIRD => 3050,
            DatabaseDriver::IBM_DB2, DatabaseDriver::OPEN_DATABASE_CONNECTIVITY => 50000,
        };
    }

    protected function generateDsn(): string{
        return match ($this->driverType){
            // MySQL / MariaDB
            DatabaseDriver::MYSQL => "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4",
            DatabaseDriver::POSTGRES => "pgsql:host={$this->host};port={$this->port};dbname={$this->database}",
            DatabaseDriver::SQL_LITE => "sqlite:{$this->database}",
            DatabaseDriver::MS_SQL_SERVER => "sqlsrv:Server={$this->host},{$this->port};Database={$this->database}",
            DatabaseDriver::MS_SQL_SERVER_LINUX => "dblib:host={$this->host}:{$this->port};dbname={$this->database}",
            DatabaseDriver::ORACLE => "oci:dbname=//{$this->host}:{$this->port}/{$this->database}",
            DatabaseDriver::FIREBIRD => "firebird:dbname={$this->host}/{$this->port}:{$this->database}",
            DatabaseDriver::IBM_DB2, DatabaseDriver::OPEN_DATABASE_CONNECTIVITY => "odbc:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->database};HOSTNAME={$this->host};PORT={$this->port};PROTOCOL=TCPIP;",
            default => "mysql:host={$this->host};dbname={$this->database}"
        };
    }

    /** Send an unprepared query the database and return the result */ 
    public function query(string $query): bool{
        return new Query(query: $query, params: NULL, patbase: $this)->execute();
    }


    /**
     * Excecute a prepared statement
     * @param string $query
     * @param mixed $params
     * @return Query
     */
    public function prepare(string $query, ?array $params): Query{
        return new Query(query: $query, params: $params, patbase: $this);
    }

    /**
     * Get the current PDO connection object
     * @return \PDO|null
     */
    public function connection(): \PDO {
        return $this->connection;
    }

    /**
     * Replace the generated PDO object with your own!
     * @param \PDO $pdo
     * @return Patbase
     */
    public function setConnection(\PDO $pdo): static {
        $this->connection = $pdo;
        return $this;
    }
    public function setPDO(\PDO $pdo): static{
        return $this->setConnection($pdo);
    }


    /**
     * Connect to the database.
     * * If you want to overwrite the connection please run first `Patbase::closeConnection()`
     * @throws Exception
     * @return Patbase
     */
    public function connect(): static{
        if ($this->connection){
            return $this;
        }
        try{
            $connection = new \PDO($this->dsn, $this->username, $this->password);
            $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetchMode);
            if ($this->options){
                foreach($this->options as $optionName => $value){
                    $connection->setAttribute($optionName, $value);
                }
            }
            $this->setConnection($connection);
        }catch(PDOException $e){
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        return $this;
    }
    public function closeConnection(): void{
        $this->connection = NULL;
    }
}
?>