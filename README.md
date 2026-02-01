# PatBase
PHP database controller

## Example Usage:
```PHP
// Create a new instance
$db = new Patbase("bite_sized_projects", "test", "", "localhost", autoConnect: false, fetchMode: PDO::FETCH_ASSOC);

// Query the database + Patbase::connect() is run to prevent errors but between the instantation and the query you can modify the PDO object for example
$data = $db->query("SELECT * FROM `resume_projects`;")->fetchAll();

/** OR */

// Here you can't change the PDO object unless you REPLACE the entire object using Patbase::setConnection() - This also overwrites the current instance that was stored
$db2 = new Patbase("bite_sized_projects", "test", "", "localhost", autoConnect: true, fetchMode: PDO::FETCH_ASSOC);

// However if you want to fetch the already existing instance at $db then you can do this rather than overwriting:
$db2 = Patbase::getInstance();

```

## License
[MIT](https://mit-license.org/)