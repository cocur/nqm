cocur/nqm
=========

> Named Query Manager (NQM) helps you organise SQL queries in files.

[![Build Status](http://img.shields.io/travis/cocur/nqm.svg)](https://travis-ci.org/cocur/nqm)
[![Code Coverage](http://img.shields.io/coveralls/cocur/nqm.svg)](https://coveralls.io/r/cocur/nqm)

Features
--------

- Wrapper for PDO
- Stores queries in the filesystem
- Caches queries in arrays or using APC
- Compatible with PHP >= 5.4 and HHVM


Installation
------------

You can install `cocur/nqm` using Composer:

```shell
$ composer require cocur/nqm:@stable
```

*Tip: Use a concrete [version](https://github.com/cocur/nqm/releases) instead of `@stable`.*


Usage
-----

In order to use NQM you need to initialise the `Cocur\NQM\NQM` class with an instance of `\PDO` and a query loader. NQM
comes with a FilesystemQueryLoader query loader.

```php
use Cocur\NQM\NQM;
use Cocur\NQM\QueryLoader\FilesystemQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$pdo = new \PDO(...);
$nqm = new NQM($pdo, $loader);
```

After you have initialised the `NQM` object you can use it. Currently the class has three public methods to retrieve a
query, prepare a statement or execute a statement.

The following command will return the SQL query stored in `./queries/find-all-users.sql`.

```php
$nqm->getQuery('find-all-users');
```

NQM can also return a `\PDOStatement` object directly:

```php
$stmt = $nqm->prepare('find-all-users');
$stmt->execute();
```

Or you can immediately execute the statement:

```php
$stmt = $nqm->execute('find-user-by-id', [':id' => 42]);
```

### Query Cache

To speed up loading of queries you can use the `Cocur\NQM\QueryLoader\CacheQueryLoader` to cache queries. The cache
class implements the same interface as the other query loaders and the constructor accepts an instance of
`QueryLoaderInterface`. If a query does not exist in the cache, the cache uses this loader to load the query. For
example,

```php
use Cocur\NQM\QueryLoader\CacheQueryLoader;
use Cocur\NQM\QueryLoader\FilesystemQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$cache = new CacheQueryLoader($loader);

$pdo = new \PDO(...);
$nqm = new NQM($pdo, $cache);
```

### APC Query Cache

The `CacheQueryLoader` query loader stores cached queries in an array and therefore only on a per-request basis. While
this often suffices in CLI applications for web apps it would be better to cache queries over multiple requests.

```php
use Cocur\NQM\QueryLoader\ApcQueryLoader;
use Cocur\NQM\QueryLoader\FilesystemQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$apc = new ApcQueryLoader($loader);

$pdo = new \PDO(...);
$nqm = new NQM($pdo, $apc);
```

Additionally if you have queries that you use more than once in a single request you can stack multiple query loaders.
In the following example NQM will load queries from the array cache or if it's not cached it will look in the APC cache.
As a last resort NQM loads the query from the filesystem.

```php
use Cocur\NQM\QueryLoader\ApcQueryLoader;
use Cocur\NQM\QueryLoader\CacheQueryLoader;
use Cocur\NQM\QueryLoader\FilesystemQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$apc = new ApcQueryLoader($loader);
$cache = new CacheQueryLoader($apc);

$pdo = new \PDO(...);
$nqm = new NQM($pdo, $cache);
```

### Array Query Loader

Stores the queries in an array.

```php
use Cocur\NQM\QueryLoader\ArrayQueryLoader;

$loader = new ArrayQueryLoader(['foo' => 'SELECT ...;']);
```

### Query Collection

Sometimes you have multiple queries that are always executed together. For example, a `DROP TABLE`, `CREATE TABLE`
sequence or if you have to create temporary tables for especially complex queries. Since PDO accepts only a single
SQL statement per statement, you can use `QueryCollection` to execute multiple queries. Queries must be separated by
`#;`, which must be placed on its own line.


```sql
DROP TABLE IF EXISTS users;
#;
CREATE TABLE users (...);
```

```php
use Cocur\NQM\QueryCollection;

$collection = NQMQueryCollection($nqm);
// Just prepare the statements
$statements = $collection->prepare('drop-and-create-user-table');

// Prepare and execute the statements
$statements = $collection->execute('drop-and-create-user-table', ['foo'=>'bar']);
```

The `prepare()` and `execute()` methods return both a `Cocur\NQM\StatementCollection`. This collection class implements
`\ArrayAccess` and `\Countable`.

```php
$statements->all(); // -> \PDOStatement[]
$statements->first(); // -> \PDOStatement
$statements->last(); // -> \PDOStatement
```


### Doctrine Bridge

If you don't have a `PDO` object, but a Doctrine `EntityManager` object you can use the Doctrine bridge to create
a new `NQM` object.


```php
use Cocur\NQM\Bridge\Doctrine\NQMFactory;

$nqm = NQMFactory::createFromEntityManager($entityManager, $queryLoader);
// NQM(...) object
```


Change log
----------

### Version 0.4 (6 October 2015)

- Add `ArrayQueryLoader`

### Version 0.3 (16 February 2015)

- Add support for query collections

### Version 0.2 (11 February 2015)

- Add Doctrine bridge

### Version 0.1.2 (3 February 2015)

- Moved development packages to `require-dev` in `composer.json`

### Version 0.1.1 (26 August 2014)

- Renamed query loader class names

### Version 0.1 (28 May 2014)

- Initial release


Author
------

#### [Florian Eckerstorfer](http://florian.ec) [![Support Florian](http://img.shields.io/gittip/florianeckerstorfer.svg)](https://www.gittip.com/FlorianEckerstorfer/)

- [Twitter](http://twitter.com/Florian_)
- [App.net](http://app.net/florian)


License
-------

The MIT license applies to `cocur/nqm`. For the full copyright and license information, please view the LICENSE file
distributed with this source code.
