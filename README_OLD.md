# PatBase
PHP database controller

## Example Usage:
```PHP
// Create a new instance
$db = new Patbase("bite_sized_projects", "test", "", "localhost", autoConnect: false, fetchMode: PDO::FETCH_ASSOC);

// You can still update the connection config before the PDO object is created
$db->fetchMode = PDO::FETCH_OBJ;

// Query the database + Patbase::connect() is run to prevent errors but between the instantation and the query you can modify the PDO object for example
$data = $db->query("SELECT * FROM `resume_projects`;")->fetchAll();

/** OR */

$db->connect();
$data = $db->query("SELECT * FROM `resume_projects`;")->fetchAll();
```

## Singleton Example Usage:
```PHP
/** Singleton example */

// Here you can't change the PDO object unless you REPLACE the entire object using Patbase::setConnection() - This also overwrites the current instance that was stored
$db = new Patbase("bite_sized_projects", "test", "", "localhost", autoConnect: true, fetchMode: PDO::FETCH_ASSOC);

// fetch the already existing instance at $db then you can do this rather than overwriting:
$db2 = Patbase::getInstance();

```

## License
[MIT](https://mit-license.org/)