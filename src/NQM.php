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

/**
 * NQM
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

    /** @var QueryLoader */
    private $queryLoader;

    /**
     * Constructor.
     *
     * @param PDO         $pdo
     * @param QueryLoader $queryLoader
     */
    public function __construct(\PDO $pdo, QueryLoader $queryLoader)
    {
        $this->pdo = $pdo;
        $this->queryLoader = $queryLoader;
    }

    /**
     * Sets the PDO connection.
     *
     * @param \PDO $pdo PDO connection
     *
     * @return NQM
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Returns the PDO connection.
     *
     * @return \PDO PDO connection.
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Sets the query loader.
     *
     * @param QueryLoader $queryLoader The query loader
     *
     * @return NQM
     */
    public function setQueryLoader(QueryLoader $queryLoader)
    {
        $this->queryLoader = $queryLoader;

        return $this;
    }

    /**
     * Returns the query loader.
     *
     * @return QueryLoader The query loader.
     */
    public function getQueryLoader()
    {
        return $this->queryLoader;
    }

    /**
     * Returns the query with the given name.
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
     * @param string $name    Name of a query.
     * @param array  $options Options, will be used to call `\PDO::prepare()`.
     *
     * @return \PDOStatement
     */
    public function prepare($name, $options = null)
    {
        return $this->pdo->prepare($this->getQuery($name), $options);
    }

    /**
     * Executes the named query with the given parameters.
     *
     * @param string $name       Name of query.
     * @param array  $parameters List of parameters to bind to the statement.
     * @param array  $options    Options, will be used to call `\PDO::prepare()`.
     *
     * @return \PDOStatement
     */
    public function execute($name, $parameters = null, $options = null)
    {
        $stmt = $this->prepare($name, $options);
        $stmt->execute($parameters);

        return $stmt;
    }
}
