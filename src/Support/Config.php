<?php
namespace PatrykNamyslak\Patbase\Support;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;

/**
 * Object for the DB facade configuration
 */
final class Config{


    public function __construct(
        public readonly bool $autoLoad = true,
        public readonly DatabaseDriver $driverType = DatabaseDriver::MYSQL,
        public readonly ?string $host = NULL,
        public readonly ?string $username = NULL,
        public readonly ?string $password = NULL,
        public readonly ?string $database = NULL,
        public readonly ?int $port = NULL,
        public readonly int $fetchMode = \PDO::FETCH_OBJ,
        public readonly bool $autoConnect = true,
        public readonly array $options = [],
        public readonly ?string $envFileDirectory = null,
        ){}

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