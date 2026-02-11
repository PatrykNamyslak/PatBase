<?php
namespace PatrykNamyslak\Patbase\Enums;


enum EnvironmentVariable: string{
    case DATABASE = "DB_NAME";
    case HOST = "DB_HOST";
    case DRIVER = "DB_DRIVER";
    case USERNAME = "DB_USER";
    case PASSWORD = "DB_PASS";
    case PORT = "DB_PORT";
    case FETCH_MODE = "DB_FETCH_MODE";



    /** Name of the config option for lazy loading on Patbase::class */
    case LAZY_LOAD = "PATBASE_LAZYLOAD";
}