<?php

/**
 * This file is part of cocur/nqm.
 *
 * (c) Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocur\NQM\QueryLoader;

/**
 * Caches queries in an array. Requires another query loader as a fallback.
 *
 *      use Cocur\NQM\QueryLoader\Cache as CacheQueryLoader;
 *      use Cocur\NQM\QueryLoader\Filesystem as FilesystemQueryLoader;
 *
 *      $loader = new FilesystemQueryLoader(__DIR__.'/queries');
 *      $cache = new CacheQueryLoader($loader);
 *
 *      $pdo = new \PDO(...);
 *      $nqm = new NQM($pdo, $cache);
 *
 * @package    cocur/nqm
 * @subpackage queryloader
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class Cache
{
    /** @var QueryLoaderInterface */
    private $loader;

    /** @var string[] */
    private $queries = [];

    /**
     * @param QueryLoaderInterface $loader
     */
    public function __construct(QueryLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return QueryLoaderInterface
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
        if (isset($this->queries[$name])) {
            return true;
        }

        return $this->loader->hasQuery($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery($name)
    {
        if (isset($this->queries[$name])) {
            return $this->queries[$name];
        }

        return $this->queries[$name] = $this->loader->getQuery($name);
    }
}
