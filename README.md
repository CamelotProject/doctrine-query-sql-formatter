Doctrine Query SQL Formatter
============================

Usage
-----

```php
<?php

use Camelot\DoctrineQueryFormatter\QuerySqlFormatter;
use Doctrine\ORM\Query;

/** @var Query $query */

$formatter = new QuerySqlFormatter();
$string = $formatter->replaceQueryParameters($query->getSQL(), $query->getParameters()->toArray());

echo $string;
```
