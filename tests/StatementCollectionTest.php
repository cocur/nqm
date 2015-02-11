<?php


namespace Cocur\NQM;


use Pseudo\PdoStatement;
use RuntimeException;

class StatementCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatementCollection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new StatementCollection();
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::add()
     * @covers Cocur\NQM\StatementCollection::all()
     */
    public function addAddsStatementAndGetAllReturnsAllStatements()
    {
        $statement = new PdoStatement();
        $this->collection->add($statement);
        $result = $this->collection->all();

        $this->assertEquals(1, count($result));
        $this->assertEquals($statement, $result[0]);
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::first()
     */
    public function firstReturnsFirstStatement()
    {
        $statement1 = new PdoStatement();
        $statement2 = new PdoStatement();
        $this->collection->add($statement1);
        $this->collection->add($statement2);

        $this->assertEquals($statement1, $this->collection->first());
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::first()
     */
    public function firstThrowsExceptionBecauseNoStatement()
    {
        try {
            $this->collection->first();
            $this->assertTrue(false);
        } catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::last()
     */
    public function lastReturnsLastStatement()
    {
        $statement1 = new PdoStatement();
        $statement2 = new PdoStatement();
        $this->collection->add($statement1);
        $this->collection->add($statement2);

        $this->assertEquals($statement2, $this->collection->last());
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::count()
     */
    public function countReturnsNumberOfStatements()
    {
        $statement1 = new PdoStatement();
        $statement2 = new PdoStatement();
        $this->collection->add($statement1);
        $this->collection->add($statement2);

        $this->assertCount(2, $this->collection);
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::offsetGet()
     */
    public function offsetGetReturnsStatement()
    {
        $statement1 = new PdoStatement();
        $statement2 = new PdoStatement();
        $this->collection->add($statement1);
        $this->collection->add($statement2);

        $this->assertEquals($statement1, $this->collection[0]);
        $this->assertEquals($statement2, $this->collection[1]);
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::offsetExists()
     */
    public function offsetExistsReturnsIfStatementIsSet()
    {
        $statement1 = new PdoStatement();
        $statement2 = new PdoStatement();
        $this->collection->add($statement1);
        $this->collection->add($statement2);

        $this->assertTrue(isset($this->collection[0]));
        $this->assertTrue(isset($this->collection[1]));
        $this->assertFalse(isset($this->collection[2]));
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::last()
     */
    public function lastThrowsExceptionBecauseNoStatement()
    {
        try {
            $this->collection->last();
            $this->assertTrue(false);
        } catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::offsetSet()
     */
    public function offsetSetThrowsException()
    {
        try {
            $this->collection[0] = 'foobar';
            $this->assertTrue(false);
        } catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     * @covers Cocur\NQM\StatementCollection::offsetUnset()
     */
    public function offsetUnsetThrowsException()
    {
        try {
            unset($this->collection[0]);
            $this->assertTrue(false);
        } catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }
}
