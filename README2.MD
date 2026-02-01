# Patbase - Type-Safe Multi-Driver Database Abstraction

Stop hardcoding database drivers. Patbase provides a clean, type-safe interface for working with any PDO-supported database.

## Features

✅ **9 Database Drivers** - MySQL, PostgreSQL, SQLite, SQL Server, Oracle, and more  
✅ **Type-Safe** - PHP 8.4 enums prevent invalid drivers  
✅ **Zero Config** - Automatic DSN generation and default ports  
✅ **Lazy Loading** - Connect only when needed  
✅ **Clean API** - Fluent, modern PHP interface  
✅ **Exception Handling** - Detailed error messages  
✅ **Transaction Support** - Begin, commit, rollback  

## Installation

```bash
composer require patryknamyslak/patbase
```

## Quick Start

### MySQL
```php
use PatrykNamyslak\Patbase\Patbase;
use PatrykNamyslak\Patbase\Enums\DatabaseDriverType;

$db = new Patbase(
    driver: DatabaseDriverType::MYSQL,
    database: 'myapp',
    username: 'root',
    password: 'secret',
    host: 'localhost'
    // port: 3306 (automatic)
);

$users = $db->query("SELECT * FROM users")->fetchAll();
```

### PostgreSQL
```php
$db = new Patbase(
    driver: DatabaseDriverType::POSTGRES,
    database: 'myapp',
    username: 'postgres',
    password: 'secret',
    host: 'localhost'
    // port: 5432 (automatic)
);
```

### SQLite
```php
$db = new Patbase(
    driver: DatabaseDriverType::SQL_LITE,
    database: './database.db'
    // No username/password needed
);
```

## Prepared Statements

```php
$stmt = $db->prepare(
    "SELECT * FROM users WHERE email = :email",
    [':email' => 'user@example.com']
);

$user = $stmt->fetch();
```

## Transactions

```php
$db->beginTransaction();

try {
    $db->prepare("INSERT INTO users ...", [...]);
    $db->prepare("INSERT INTO profiles ...", [...]);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}
```

## Lazy Loading

```php
// Don't connect yet
$db = new Patbase(
    driver: DatabaseDriverType::MYSQL,
    database: 'myapp',
    username: 'root',
    password: 'secret',
    autoConnect: false
);

// Optionally modify DSN
$db->setDsn('mysql:host=127.0.0.1;port=3307;dbname=myapp');

// Connect when ready (or auto-connects on first query)
$db->connect();
```

## Supported Databases

| Database | Driver Enum | Default Port |
|----------|------------|--------------|
| MySQL / MariaDB | `DatabaseDriverType::MYSQL` | 3306 |
| PostgreSQL | `DatabaseDriverType::POSTGRES` | 5432 |
| SQLite | `DatabaseDriverType::SQL_LITE` | N/A (file) |
| SQL Server | `DatabaseDriverType::MS_SQL_SERVER` | 1433 |
| SQL Server (Linux) | `DatabaseDriverType::MS_SQL_SERVER_LINUX` | 1433 |
| Oracle | `DatabaseDriverType::ORACLE` | 1521 |
| Firebird | `DatabaseDriverType::FIREBIRD` | 3050 |
| IBM DB2 | `DatabaseDriverType::IBM_DB2` | 50000 |
| ODBC | `DatabaseDriverType::OPEN_DATABASE_CONNECTIVITY` | 50000 |

## Advanced Usage

### Custom PDO Options
```php
$db = new Patbase(
    driver: DatabaseDriverType::MYSQL,
    database: 'myapp',
    username: 'root',
    password: 'secret',
    options: [
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
);
```

### Check Connection Status
```php
if (!$db->isConnected()) {
    $db->connect();
}
```

### Get Last Insert ID
```php
$db->prepare("INSERT INTO users ...", [...]);
$userId = $db->lastInsertId();
```

### Access PDO Directly (Advanced)
```php
$pdo = $db->getPDO();
// Use native PDO methods if needed
```

## License

MIT

## Author

**Patryk Namyslak** - [GitHub](https://github.com/PatrykNamyslak)