<?php
namespace PatrykNamyslak\Patbase\Support;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;

/**
 * Object for the DB facade configuration
 */
final class Config{


    public function __construct(
        public bool $autoLoad = true,
        public DatabaseDriver $driverType = DatabaseDriver::MYSQL,
        public ?string $host = NULL,
        public ?string $username = NULL,
        public ?string $password = NULL,
        public ?string $database = NULL,
        public ?int $port = NULL,
        public int $fetchMode = \PDO::FETCH_OBJ,
        public bool $lazyLoad = false,
        public array $options = [],
        ){}

    /** Sets the driver type and if one is not found with the value of $type, it defaults to MYSQL */
    public function driverType(string $type = DatabaseDriver::MYSQL->value): void{
        $this->driverType = DatabaseDriver::tryFrom($type) ?? DatabaseDriver::MYSQL;
    }


    /**
     * Set the fetch mode by using just the name, rather than it being `\PDO::FETCH_OBJ` it can be just `OBJ` and `\PDO::FETCH_OBJ` will be assigned to `Config::$fetchMode`
     * @param string $driverType
     * @return int
     */
    public static function fetchMode(string $mode): int{
        return match($mode){
            "OBJ" => \PDO::FETCH_OBJ,
            "ASSOC", "ARRAY" => \PDO::FETCH_ASSOC,
            default => \PDO::FETCH_OBJ,
        };
    }
}