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
 * QueryLoaderInterfaceInterface
 *
 * @package    cocur/nqm
 * @subpackage queryloader
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
interface QueryLoaderInterface
{
    /**
     * Returns whether a query with the given name exists.
     *
     * @param string $name Name of a query.
     *
     * @return boolean `true` if the query exists, `false` otherwise.
     */
    public function hasQuery($name);

    /**
     * Returns the query with the given name.
     *
     * @param string $name Name of a query.
     *
     * @return string SQL code of the query with the given name.
     *
     * @throws Cocur\NQM\Exception\QueryNotExistsException if no query with the given name exists.
     */
    public function getQuery($name);
}
