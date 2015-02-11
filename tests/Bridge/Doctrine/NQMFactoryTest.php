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

use Mockery as m;
use Pseudo\Pdo;

class NQMFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\NQM\Bridge\Doctrine\NQMFactory::createFromEntityManager()
     */
    public function createFromEntityManager()
    {
        $conn = m::mock('Doctrine\DBAL\Connection');
        $conn->shouldReceive('getWrappedConnection')->andReturn($this->getMockPdo());

        $em = m::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('getConnection')->andReturn($conn);

        $queryLoader = m::mock('Cocur\NQM\QueryLoader\QueryLoaderInterface');

        $this->assertInstanceOf('Cocur\NQM\NQM', NQMFactory::createFromEntityManager($em, $queryLoader));
    }

    /**
     * @return \PDO
     */
    protected function getMockPdo()
    {
        return new Pdo();
    }
}
