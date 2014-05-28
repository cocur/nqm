<?php

namespace Cocur\NQM\QueryLoader;

use \Mockery as m;
use Cocur\NQM\QueryLoader\Apc;

/**
 * ApcTest
 *
 * @group unit
 */
class ApcTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cocur\NQM\QueryLoader\QueryLoaderInterface */
    private $loader;

    /** @var Apc */
    private $cache;

    public function setUp()
    {
        apc_clear_cache();
        $this->loader = m::mock('Cocur\NQM\QueryLoader\QueryLoaderInterface');
        $this->cache = new Apc($this->loader, 'nqm_test.');
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::__construct()
     * @covers Cocur\NQM\QueryLoader\Apc::getLoader()
     */
    public function getLoader()
    {
        $this->assertEquals($this->loader, $this->cache->getLoader());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::getApcPrefix()
     */
    public function getApcPrefixReturnsApcPrefix()
    {
        $this->assertEquals('nqm_test.', $this->cache->getApcPrefix());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::hasQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     */
    public function hasQueryReturnsTrueIfQueryIsCached()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');
        $this->loader->shouldReceive('hasQuery')->never();
        $this->cache->getQuery('foo'); // load query in cache

        $this->assertTrue($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::hasQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     */
    public function hasQueryReturnsTrueIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(true);

        $this->assertTrue($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::hasQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     */
    public function hasQueryReturnsFalseIfQueryIsNotCachedAndNotExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(false);

        $this->assertFalse($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::getQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     */
    public function getQueryReturnsQueryIfQueryIsCached()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');
        $this->cache->getQuery('foo'); // load query in cache

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::getQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     */
    public function getQueryReturnsQueryIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Apc::getQuery()
     * @covers Cocur\NQM\QueryLoader\Apc::getApcName()
     * @expectedException Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryThrowsExceptionIfQueryDoesNotExist()
    {
        $this->loader->shouldReceive('getQuery')->once()->andThrow('Cocur\NQM\Exception\QueryNotExistsException');

        $this->cache->getQuery('foo');
    }
}
