<?php

/**
 * This file is part of cocur/nqm.
 *
 * (c) Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocur\NQM;

use Cocur\NQM\Exception\QueryNotExistsException;
use Cocur\NQM\QueryLoader\QueryLoaderInterface;
use PDO;

/**
 * Named Query Manager can be used to execute PDO requests based on query names. The queries are stored in the
 * filesystem (or using a cache like APC) and are retrieved when needed.
 *
 *     use Cocur\NQM\NQM;
 *     use Cocur\NQM\QueryLoader\FilesystemQueryLoader as FilesystemQueryLoader;
 *
 *     $loader = new FilesystemQueryLoader(__DIR__.'/queries');
 *     $pdo = new \PDO(...);
 *     $nqm = new NQM($pdo, $loader);
 *
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class NQM
{
    /** @var \PDO */
    private $pdo;

    /** @var QueryLoaderInterface */
    private $queryLoader;

    /**
     * Constructor.
     *
     * @param PDO         $pdo
     * @param QueryLoaderInterface $queryLoader
     */
    public function __construct(PDO $pdo, QueryLoaderInterface $queryLoader)
    {
        $this->pdo = $pdo;
        $this->queryLoader = $queryLoader;
    }

    /**
     * Sets the PDO connection.
     *
     * @param PDO $pdo PDO connection
     *
     * @return NQM
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Returns the PDO connection.
     *
     * @return PDO PDO connection.
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Sets the query loader.
     *
     * @param QueryLoaderInterface $queryLoader The query loader
     *
     * @return NQM
     */
    public function setQueryLoader(QueryLoaderInterface $queryLoader)
    {
        $this->queryLoader = $queryLoader;

        return $this;
    }

    /**
     * Returns the query loader.
     *
     * @return QueryLoaderInterface The query loader.
     */
    public function getQueryLoader()
    {
        return $this->queryLoader;
    }

    /**
     * Returns the query with the given name.
     *
     *     $nqm->getQuery('find-all-users');
     *
     * @param string $name Name of a query.
     *
     * @return string SQL code of the query with the given name.
     *
     * @throws QueryNotExistsException if no query with the given name exists.
     */
    public function getQuery($name)
    {
        return $this->queryLoader->getQuery($name);
    }

    /**
     * Prepares a named query for execution.
     *
     *     $stmt = $nqm->prepare('find-all-users');
     *     $stmt->execute();
     *
     * @param string $name    Name of a query.
     * @param array  $options Options, will be used to call `\PDO::prepare()`.
     *
     * @return \PDOStatement
     */
    public function prepare($name, $options = [])
    {
        return $this->pdo->prepare($this->getQuery($name), $options);
    }

    /**
     * Executes the named query with the given parameters.
     *
     *     $stmt = $nqm->execute('find-user-by-id', [':id' => 42]);
     *
     * @param string $name       Name of query.
     * @param array  $parameters List of parameters to bind to the statement.
     * @param array  $options    Options, will be used to call `\PDO::prepare()`.
     *
     * @return \PDOStatement
     */
    public function execute($name, $parameters = [], $options = [])
    {
        $stmt = $this->prepare($name, $options);
        $stmt->execute($this->convertParameters($parameters));

        return $stmt;
    }

    /**
     * Prepends a colon to each parameter key.
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function convertParameters(array $parameters)
    {
        $new = [];
        foreach ($parameters as $key => $value) {
            if (':' !== substr($key, 0, 1)) {
                $key = ':'.$key;
            }
            $new[$key] = $value;
        }

        return $new;
    }
}
