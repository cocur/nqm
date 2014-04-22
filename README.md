cocur/nqm
=========

> Named Query Manager helps you SQL organise queries in files.

[![Build Status](http://img.shields.io/travis/cocur/nqm.svg)](https://travis-ci.org/cocur/nqm)
[![Code Coverage](http://img.shields.io/coveralls/cocur/nqm.svg)](https://coveralls.io/r/cocur/nqm)

Features
--------

- Wrapper for PDO
- Stores queries on the filesystem
- Compatible with PHP >= 5.4 and HHVM


Installation
------------

You can install `cocur/nqm` using Composer:

```shell
$ composer require cocur/nqm:@dev
```

*Currently no stable version of NQM exists.*


Usage
-----

In order to use NQM you need to initialise the `Cocur\NQM\NQM` class with an instance of `\PDO` and a query loader. NQM comes with a Filesystem query loader.

```php
use Cocur\NQM\NQM;
use Cocur\NQM\QueryLoader\Filesystem as FilesystemQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$pdo = new \PDO(...);
$nqm = new NQM($pdo, $loader);
```

After you have initialised the `NQM` object you can use it. Currently the class has three public methods to retrieve a query, prepare a statement or execute a statement.

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

To speed up loading of queries you can use the `Cocur\NQM\QueryLoader\Cache` to cache queries. The cache class implements the same interface as the other query loaders and the constructor accepts an instance of `QueryLoaderInterface`. If a query does not exist in the cache, the cache uses this loader to load the query. For example,

```php
use Cocur\NQM\QueryLoader\Filesystem as FilesystemQueryLoader;
use Cocur\NQM\QueryLoader\Cache as CacheQueryLoader;

$loader = new FilesystemQueryLoader(__DIR__.'/queries');
$cache = new CacheQueryLoader($loader);

$pdo = new \PDO(...);
$nqm = new NQM($pdo, $loader);
```


Changelog
---------


Author
------

#### [Florian Eckerstorfer](http://florian.ec) [![Support Florian](http://img.shields.io/gittip/florianeckerstorfer.svg)](https://www.gittip.com/FlorianEckerstorfer/)

- [Twitter](http://twitter.com/Florian_)
- [App.net](http://app.net/florian)


License
-------

The MIT license applies to `cocur/nqm`. For the full copyright and license information, please view the LICENSE file distributed with this source code.
