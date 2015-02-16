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

use Mockery as m;
use Pseudo\Pdo;

/**
 * QueryCollectionTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class QueryCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryCollection
     */
    private $collection;

    /**
     * @var \Mockery\MockInterface|\Cocur\NQM\NQM
     */
    private $nqm;

    public function setUp()
    {
        $this->nqm        = $this->getMockNQM();
        $this->collection = new QueryCollection($this->nqm);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryCollection::__construct()
     * @covers Cocur\NQM\QueryCollection::getQueries()
     */
    public function getQueriesReturnsListOfQueries()
    {
        $this->nqm
            ->shouldReceive('getQuery')
            ->with('drop-and-create')
            ->once()
            ->andReturn("drop table x;\n#;\ncreate table x;");

        $queries = $this->collection->getQueries('drop-and-create');

        $this->assertCount(2, $queries);
        $this->assertEquals('drop table x;', $queries[0]);
        $this->assertEquals('create table x;', $queries[1]);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryCollection::__construct()
     * @covers Cocur\NQM\QueryCollection::getQueries()
     */
    public function getQueriesDoesNotSplitSeparatorInQuery()
    {
        $this->nqm->shouldReceive('getQuery')->with('drop-and-create')->once()->andReturn("select '#;' from x;");

        $queries = $this->collection->getQueries('drop-and-create');

        $this->assertCount(1, $queries);
        $this->assertEquals("select '#;' from x;", $queries[0]);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryCollection::prepare()
     */
    public function preparePreparesMultipleStatements()
    {
        $pdo = $this->getMockPdo();
        $pdo->mock('DROP table x;', []);
        $pdo->mock('CREATE table x;', []);
        $this->nqm->shouldReceive('getPdo')->once()->andReturn($pdo);
        $this->nqm
            ->shouldReceive('getQuery')
            ->with('drop-and-create')
            ->once()
            ->andReturn("DROP table x;\n#;\nCREATE table x;");

        $statements = $this->collection->prepare('drop-and-create');

        $this->assertCount(2, $statements);
        $this->assertInstanceOf('\PDOStatement', $statements[0]);
        $this->assertInstanceOf('\PDOStatement', $statements[1]);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryCollection::__construct()
     * @covers Cocur\NQM\QueryCollection::execute()
     * @covers Cocur\NQM\QueryCollection::getQueryParameters()
     */
    public function executeExecutesMultipleStatements()
    {
        $pdo = $this->getMockPdo();
        $pdo->mock('DROP table x;', []);
        $pdo->mock('CREATE table x;', []);
        $this->nqm->shouldReceive('getPdo')->once()->andReturn($pdo);
        $this->nqm
            ->shouldReceive('getQuery')
            ->with('drop-and-create')
            ->once()
            ->andReturn("DROP table x;\n#;\nCREATE table x;");

        $statements = $this->collection->execute('drop-and-create');

        $this->assertCount(2, $statements);
        $this->assertInstanceOf('\PDOStatement', $statements[0]);
        $this->assertInstanceOf('\PDOStatement', $statements[1]);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryCollection::__construct()
     * @covers Cocur\NQM\QueryCollection::execute()
     * @covers Cocur\NQM\QueryCollection::getQueryParameters()
     */
    public function executeExecutesMultipleStatementsWithDifferentParameters()
    {
        $pdo = $this->getMockPdo();
        $pdo->mock('SELECT :foo1;', []);
        $pdo->mock('SELECT :foo2;', []);
        $this->nqm->shouldReceive('getPdo')->once()->andReturn($pdo);
        $this->nqm
            ->shouldReceive('getQuery')
            ->with('select2')
            ->once()
            ->andReturn("SELECT :foo1;\n#;\nSELECT :foo2;");

        $statements = $this->collection->execute('select2', ['foo1'=>'bar1', 'foo2'=>'bar2']);

        $this->assertCount(2, $statements);
        $this->assertInstanceOf('\PDOStatement', $statements[0]);
        $this->assertCount(1, $statements[0]->getBoundParams());
        $this->assertArrayHasKey(':foo1', $statements[0]->getBoundParams());
        $this->assertArrayNotHasKey('foo2', $statements[0]->getBoundParams());
        $this->assertInstanceOf('\PDOStatement', $statements[1]);
        $this->assertCount(1, $statements[1]->getBoundParams());
        $this->assertArrayHasKey(':foo2', $statements[1]->getBoundParams());
        $this->assertArrayNotHasKey('foo1', $statements[1]->getBoundParams());
    }

    /**
     * @return \Mockery\MockInterface|\Cocur\NQM\NQM
     */
    protected function getMockNQM()
    {
        return m::mock('Cocur\NQM\NQM');
    }

    /**
     * @return \Pseudo\Pdo
     */
    protected function getMockPdo()
    {
        return new Pdo();
    }
}
