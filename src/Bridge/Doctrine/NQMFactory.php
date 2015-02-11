<?php

/**
 * This file is part of cocur/nqm.
 *
 * (c) Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocur\NQM\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Cocur\NQM\QueryLoader\QueryLoaderInterface;
use Cocur\NQM\NQM;

/**
 * NQMFactory
 *
 * @package    cocur/nqm
 * @subpackage bridge
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2013-2015 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class NQMFactory
{
    /**
     * @param EntityManager        $em
     * @param QueryLoaderInterface $queryLoader
     *
     * @return NQM
     */
    public static function createFromEntityManager(EntityManager $em, QueryLoaderInterface $queryLoader)
    {
        return new NQM($em->getConnection()->getWrappedConnection(), $queryLoader);
    }
}
