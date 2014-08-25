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
 * Caches queries in APC. Requires another query loader as fallback.
 *
 *      use Cocur\NQM\QueryLoader\ApcQueryLoader;
 *      use Cocur\NQM\QueryLoader\FilesystemQueryLoader;
 *
 *      $loader = new FilesystemQueryLoader(__DIR__.'/queries');
 *      $apc = new ApcQueryLoader($loader);
 *
 *      $pdo = new \PDO(...);
 *      $nqm = new NQM($pdo, $apc);
 *
 * @package    cocur/nqm
 * @subpackage queryloader
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class ApcQueryLoader implements QueryLoaderInterface
{
    /** @var QueryLoaderInterface */
    private $loader;

    /** @var string */
    private $apcPrefix = 'nqm_query.';

    /**
     * @param QueryLoaderInterface $loader
     * @param string               $apcPrefix
     */
    public function __construct(QueryLoaderInterface $loader, $apcPrefix = null)
    {
        $this->loader = $loader;
        if ($apcPrefix) {
            $this->apcPrefix = $apcPrefix;
        }
    }

    /**
     * @return QueryLoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return string The prefix used for APC.
     */
    public function getApcPrefix()
    {
        return $this->apcPrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function hasQuery($name)
    {
        if (apc_exists($this->getApcName($name))) {
            return true;
        }

        return $this->loader->hasQuery($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery($name)
    {
        if (apc_exists($this->getApcName($name))) {
            return apc_fetch($this->getApcName($name));
        }

        $query = $this->loader->getQuery($name);
        apc_store($this->getApcName($name), $query);

        return $query;
    }

    /**
     * Returns the name with the configured prefix.
     *
     * @param string $name Internal name of query.
     *
     * @return string APC name of query.
     */
    protected function getApcName($name)
    {
        return $this->apcPrefix.$name;
    }
}
