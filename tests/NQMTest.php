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

use \Mockery as m;
use Pseudo\Pdo;

use Cocur\NQM\NQM;

/**
 * NQMTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class NQMTest extends \PHPUnit_Framework_TestCase
{
    /** @var NQM */
    private $nqm;

    /** @var \PDO */
    private $pdo;

    /** @var Cocur\NQM\QueryLoader */
    private $queryLoader;

    public function setUp()
    {
        $this->pdo         = $this->getMockPdo();
        $this->queryLoader = $this->getMockQueryLoader();

        $this->nqm = new NQM($this->pdo, $this->queryLoader);
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::__construct()
     * @covers Cocur\NQM\NQM::getPdo()
     * @covers Cocur\NQM\NQM::getQueryLoader()
     */
    public function constructorShouldSetPdoAndQueryLoader()
    {
        $pdo = $this->getMockPdo();
        $loader = $this->getMockQueryLoader();

        $nqm = new NQM($pdo, $loader);

        $this->assertEquals($pdo, $nqm->getPdo());
        $this->assertEquals($loader, $nqm->getQueryLoader());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::setPdo()
     * @covers Cocur\NQM\NQM::getPdo()
     */
    public function setPdoShouldSetPdo()
    {
        $pdo = $this->getMockPdo();
        $this->nqm->setPdo($pdo);
        $this->assertEquals($pdo, $this->nqm->getPdo());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::setQueryLoader()
     * @covers Cocur\NQM\NQM::getQueryLoader()
     */
    public function setQueryLoaderShouldSetQueryLoader()
    {
        $loader = $this->getMockQueryLoader();
        $this->nqm->setQueryLoader($loader);
        $this->assertEquals($loader, $this->nqm->getQueryLoader());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::getQuery()
     */
    public function getQueryShouldReturnQuery()
    {
        $this->queryLoader->shouldReceive('getQuery')->with('foo')->once()->andReturn('SELECT * FROM table;');

        $this->assertEquals('SELECT * FROM table;', $this->nqm->getQuery('foo'));
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::prepare()
     */
    public function prepareShouldReturnPdoStatement()
    {
        $this->pdo->mock('SELECT * FROM table;', []);
        $this->queryLoader->shouldReceive('getQuery')->with('foo')->once()->andReturn('SELECT * FROM table;');

        $stmt = $this->nqm->prepare('foo');
        $this->assertInstanceOf('\PDOStatement', $stmt);
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\NQM::execute()
     */
    public function executeShouldExecutePdoStatement()
    {
        $this->pdo->mock('SELECT * FROM table WHERE key = :key;', [ [ 'key' => 'foo' ] ]);
        $this->queryLoader
            ->shouldReceive('getQuery')
            ->with('foo')
            ->once()
            ->andReturn('SELECT * FROM table WHERE key = :key;');

        $stmt = $this->nqm->execute('foo', [ 'key' => 'foo' ]);
        $this->assertInstanceOf('\PDOStatement', $stmt);
        $this->assertEquals([ 'key' => 'foo' ], $stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * @return \PDO
     */
    protected function getMockPdo()
    {
        return new Pdo();
    }

    /**
     * @return Cocur\NQM\QueryLoader
     */
    protected function getMockQueryLoader()
    {
        return m::mock('Cocur\NQM\QueryLoader');
    }
}
