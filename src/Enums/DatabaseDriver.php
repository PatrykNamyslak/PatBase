<?php
namespace PatrykNamyslak\Patbase\Enums;

enum DatabaseDriver: string{

    /**
     * Use for: MYSQL + MariaDB
     */
    case MYSQL = "mysql";
    case POSTGRES = "pgsql";
    case SQL_LITE = "sqlite";
    case MS_SQL_SERVER = "sqlsrv";
    case MS_SQL_SERVER_LINUX = "dblib";
    case ORACLE = "oci";
    case FIREBIRD = "firebird";
    case IBM_DB2 = "ibm";
    case OPEN_DATABASE_CONNECTIVITY = "odbc";
}