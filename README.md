#Utils

A home for miscellaneous scripts, classes and other bits 'n' bobs. 

##DatabaseManager.php

A basic util/registry for parsing and storing 
database credentials from an ini file.

```php
// read an .ini file
$dm = new DatabaseManager('/path/to/db-params.ini');

// validate key and connect to schema:
if ($dm->has('dev_server')) {
    $dm->connect('dev_server', 'my_schema'); // using mysql_connect() o_O
}

// or just grab params as an object and use it as you see fit
$params = $dm->get('dev_server');
$dsn = sprintf('mysql:host=%s;dbname=%s', $params->host, $schema);
$db = new PDO($dsn, $params->user, $params->pass); // PDO FTW
```

##accessor_generator.php

A command line util for quickly generating 
boilerplate getter/setter methods, e.g:

```bash
$ ./accessor_generator.php -f 'first_name last_name'
```
