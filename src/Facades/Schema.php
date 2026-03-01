<?php
namespace PatrykNamyslak\Patbase\Facades;


/**
 * MySQL table Schema Generator
 */
abstract class Schema{

    /**
     * Create a table schema from blueprint
     * @param string $table The table name of the table that you are generating schema for.
     * @param callable $blueprint An annonymous function / callback provided 
     * @return void
     */
    public static function create(string $table, callable $blueprint): void{

    }
}