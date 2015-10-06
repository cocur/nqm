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

use Cocur\NQM\Exception\QueryNotExistsException;

/**
 * Stories queries in an array.
 *
 *     use Cocur\NQM\QueryLoader\ArrayQueryLoader;
 *
 *     $cache = new ArrayQueryLoader(['foo' => 'SELECT ...;']);
 *
 *     $pdo = new \PDO(...);
 *     $nqm = new NQM($pdo, $cache);
 *
 * @package    cocur/nqm
 * @subpackage queryloader
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013-2015 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class ArrayQueryLoader implements QueryLoaderInterface
{
    /** @var string[] */
    private $queries = [];

    /**
     * @param string[] $queries
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $queries)
    {
        $this->queries = $queries;
    }

    /**
     * {@inheritDoc}
     */
    public function hasQuery($name)
    {
        if (isset($this->queries[$name])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery($name)
    {
        if (isset($this->queries[$name])) {
            return $this->queries[$name];
        }

        throw new QueryNotExistsException(sprintf('There exists no query with the name "%s".', $name));
    }
}
