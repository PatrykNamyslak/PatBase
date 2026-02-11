<?php
namespace PatrykNamyslak\Patbase\Support;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;

/**
 * Object for the DB facade configuration
 */
final class Config{
    public ?DatabaseDriver $driverType = DatabaseDriver::MYSQL;
    public ?string $host = NULL;
    public ?string $username = NULL;
    public ?string $password = NULL;
    public ?string $database = NULL;
    public int|string|null $port = NULL;
    public ?int $fetchMode = \PDO::FETCH_OBJ;
    public bool $autoConnect = true;
    public ?array $options = [];
    public string $envFileDirectory;

    public function __construct(public ?bool $autoLoad = true){
        $this->envFileDirectory = $_SERVER["DOCUMENT_ROOT"] . "/";
    }

    /** Sets the driver type and if one is not found with the value of $type, it defaults to MYSQL */
    public function driverType(string $type = DatabaseDriver::MYSQL->value): void{
        $this->driverType = DatabaseDriver::tryFrom($type) ?? DatabaseDriver::MYSQL;
    }


    /**
     * Set the fetch mode by using just the name, rather than it being `\PDO::FETCH_OBJ` it can be just `OBJ` and `\PDO::FETCH_OBJ` will be assigned to `Config::$fetchMode`
     * @param string $driverType
     * @return void
     */
    public function fetchMode(string $mode): void{
        $this->fetchMode = match($mode){
            "OBJ" => \PDO::FETCH_OBJ,
            "ASSOC", "ARRAY" => \PDO::FETCH_ASSOC,
            default => \PDO::FETCH_OBJ,
        };
    }
}