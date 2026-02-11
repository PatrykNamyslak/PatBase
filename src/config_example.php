<?php

use PatrykNamyslak\Patbase\Facades\DB;
use PatrykNamyslak\Patbase\Support\Config;

/** 
 * Generate config 
 * */

/**
 * Option 1: Manual Mapping
 * */
$keys = DB::configKeys();
$values = [
    // You have to remember the structure
];
$config = array_fill_keys($keys, $values);

// For demonstration purposes only.
unset($keys);
unset($values);
unset($config);

/**
 * Option 2: Automatic config generation by using helper function and autocomplete by passing in the parameters or leave it blank for automatic generation using environment variables
 */

// Loads from env
$configEnv = new Config(autoLoad: true);
DB::configure($configEnv);
// or
$config = new Config();
$config->host = "localhost";
$config->autoConnect = true;
$config->database = "database";
// and so on for the rest of the config...

DB::connect($config);

// You can overwrite the config by passing a valid one here:


// or just run it with the already setup config you set earlier:
DB::connect();