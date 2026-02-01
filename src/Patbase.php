<?php
namespace PatrykNamyslak;

use Exception;
use PDOException;
/**
 * * Singleton class for managing database connections and queries
 */
class Patbase {
    protected(set) ?\PDO $connection = NULL;


    // Singleton Design: 
    private static ?self $instance = NULL;
    private static bool $isSingleton = false;
    public static function getInstance(){
        return self::$instance;
    }
    protected function setInstance(){
        if (!self::$instance){
            self::$instance = $this;
        }
    }

    /** WORK IN PROGRESS TOGGLE */
    // public static function isSingleton(bool $value){
    //     self::$isSingleton = $value;
    // }
    //

    public string $dsn;
    public function __construct(public string $database, protected string $username, protected string $password, string $host='localhost', ?string $dsn = null, public int $fetchMode = \PDO::FETCH_ASSOC, bool $autoConnect = true) {
        $this->dsn = $dsn ?: "mysql:host={$host};dbname={$this->database}";
        if ($autoConnect){
            $this->connect();
        }
        $this->setInstance();
        
        /** WORK IN PROGRESS TOGGLE */
        // if (self::$isSingleton){
        //     $this->setInstance();
        // }
    }
    // Query the database and return results
    public function query(string $query): Query{
        return new Query(query: $query, params: NULL);
    }
    /**
     * Alias for $this->query();
     */
    public function prepare(string $query, ?array $params): Query{
        return new Query(query: $query, params: $params);
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


    public function connect(): static{
        if ($this->connection){
            return $this;
        }
        try{
            $connection = new \PDO($this->dsn, $this->username, $this->password);
            $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetchMode);
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