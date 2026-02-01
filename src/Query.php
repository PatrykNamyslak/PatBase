<?php
namespace PatrykNamyslak;

class Query extends Patbase{

    protected function __construct(protected string $query, protected ?array $params = NULL){
        // Make sure the connection is established before a query is run as by now we can assume the user forgot to connect to the database if it's still null
        if (!$this->connection){
            $Patbase = Patbase::getInstance()->connect();
            $this->connection = $Patbase->connection();
        }
    }

    public function fetch(): mixed{
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->fetch();
    }

    /**
     * Executes a prepared statement with and without parameters
     */
    public function fetchAll(): mixed{
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }
    public function execute(): bool{
        $stmt = $this->connection->prepare($this->query);
        return $stmt->execute($this->params);
    }
}