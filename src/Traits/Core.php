<?php
namespace PatrykNamyslak\Patbase\Traits;

use Dotenv\Dotenv;
use PatrykNamyslak\Patbase\Enums\DatabaseDriver;
use PatrykNamyslak\Patbase\Enums\EnvironmentVariable;
use PatrykNamyslak\Patbase\Support\Config;
trait Core{
    protected static Config $config;
    
    public static function configureFromEnv(){
        $keys = self::configKeys();
        foreach ($keys as $key){

        }
    }

    public static function loadEnv(string $envFileDirectory){
        $dotenv = Dotenv::createImmutable($envFileDirectory);
        $dotenv->load();

        $dotenv
        ->required(EnvironmentVariable::DRIVER->value)
        ->notEmpty()
        ->allowedValues(array_column(DatabaseDriver::cases(), "value"));
        // Required to be in the .env file and cannot be empty
        $dotenv
        ->required([EnvironmentVariable::DATABASE->value, EnvironmentVariable::USERNAME->value, EnvironmentVariable::PASSWORD->value])->notEmpty();
        // Required to be inside of the .env file but can be empty or null!
        $dotenv
        ->required([EnvironmentVariable::PORT->value]);
        $dotenv
        ->required([EnvironmentVariable::FETCH_MODE->value])
        ->notEmpty()
        ->allowedValues(["OBJ", "ASSOC"]);
        $dotenv
        ->required([EnvironmentVariable::LAZY_LOAD->value])
        ->isBoolean()
        ->notEmpty();
    }



/**
     * Returns an array with the structure expected for `Patbase::constructWithConfig(array $config)`
     * @return string[]
     */
    public static function configKeys(): array{
        return [
            // Database driver i.e DatabaseDriver::MYSQL
            "driver",
            "host",
            "username",
            "database",
            "port",
            // Set the PDO fetch mode, such as PDO::FETCH_OBJ
            "fetchMode",
            // Toggle lazy loading
            "autoConnect",
            // Extra options for PDO
            "options",
        ];
    }
}