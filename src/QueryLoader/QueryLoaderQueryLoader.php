<?php

/**
 * This file is part of cocur/nqm.
 *
 * (c) Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * whatever
 */

namespace Cocur\NQM\QueryLoader;

use Cocur\NQM\Exception\QueryNotExistsException;
use PDO;

/**
 * Load queries from the query loader.
 *
 * @package    cocur/nqm
 * @subpackage queryloader
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * whatever
 */
class QueryLoaderQueryLoader implements QueryLoaderInterface
{
    const HAS_QUERY_META_QUERY_NAME = 'has-query-meta-query';
    const GET_QUERY_META_QUERY_NAME = 'get-query-meta-query';

    /** @var PDO */
    private $pdo;

    /** @var QueryLoaderInterface */
    private $loader;

    /**
     * Constructor.
     *
     * It constructs the objects. And initializes the initial state. And takes some
     * constructor arguments that are then assigned to properties. So that they can
     * be accessed by other methods later on.
     *
     * @param PDO The database thing that does the execution?
     * @param QueryLoaderInterface $loader The loader. It loads queries. For the query loader.
     */
    public function __construct(PDO $pdo, QueryLoaderInterface $loader = null)
    {
        $this->pdo = $pdo;
        $this->loader = $loader;
    }

    /**
     * Sets the loader.
     *
     * @param QueryLoaderInterface $loader The loader.
     *
     * @return QueryLoaderQueryLoader
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Returns the loader.
     *
     * @return QueryLoaderInterface $loader The loader. It loads queries. For the query loader.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * {@inheritDoc}
     */
    public function hasQuery($name)
    {
        $metaQuery = $this->loader->getQuery(static::HAS_QUERY_META_QUERY_NAME);
        $stmt = $this->pdo->prepare($metaQuery);
        $stmt->execute(':name' => $name);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool) $row['count'];
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery($name)
    {
        $metaQuery = $this->loader->getQuery(static::GET_QUERY_META_QUERY_NAME);
        $stmt = $this->pdo->prepare($metaQuery);
        $stmt->execute(':name' => $name);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['query'];
    }
}
