DataDX
======

RadHam DataDX is the pseudo-sequel to PDOConn OMG! Long story short... it's a PDO helper class to make data access easy.
Currently it's an incomplete mess but I'll get it caught up here someday... (Hopefully sooner than later.)

Coded in compliance with PSR-2: http://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

A Quick And Dirty Example
-------------------------

```php
<?php

// Auto-load the library via Composer.
require 'vendor/autoload.php';

// Instantiate the object; for great justice!
$dx = new RadHam\DataDx('mysql', '127.0.0.1', 'username', 'password', 'database_name');

// Returns an array of the DB's table names.
$dx->getTableNames();

// Returns an array of the queried table's column names.
$dx->getColNames('table_name');

// Returns a comma separated string with the identifiers quoted in 
// the DB specific syntax. 
$dx->quoteIdentifiers(['hello', 'world']);
// For MySQL/MariaDB this would return:
#>> `hello`, `world`

// You can also pass a single argument as a string...
$dx->quoteIdentifiers('ohhai');
#>> `ohhai`

// Fetch your query as JSON (because JSON is awesome, duh!)
$dx->getJson('SELECT * FROM `db_name`.`db_table`');

// Or just fetch it as a plain Jane associative array.
$dx->get('SELECT * FROM `db_table`');

// Close the DB connection.
$dx = null;
```
