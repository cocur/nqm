<?php

namespace Cocur\NQM\QueryLoader;

use \Mockery as m;
use Cocur\NQM\QueryLoader\Cache;

/**
 * CacheTest
 *
 * @group unit
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cocur\NQM\QueryLoader\QueryLoaderInterface */
    private $loader;

    /** @var Cache */
    private $cache;

    public function setUp()
    {
        $this->loader = m::mock('Cocur\NQM\QueryLoader\QueryLoaderInterface');
        $this->cache = new Cache($this->loader);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::__construct()
     * @covers Cocur\NQM\QueryLoader\Cache::getLoader()
     */
    public function getLoader()
    {
        $this->assertEquals($this->loader, $this->cache->getLoader());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::hasQuery()
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
     * @covers Cocur\NQM\QueryLoader\Cache::hasQuery()
     */
    public function hasQueryReturnsTrueIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(true);

        $this->assertTrue($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::hasQuery()
     */
    public function hasQueryReturnsFalseIfQueryIsNotCachedAndNotExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(false);

        $this->assertFalse($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::getQuery()
     */
    public function getQueryReturnsQueryIfQueryIsCached()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');
        $this->cache->getQuery('foo'); // load query in cache

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::getQuery()
     */
    public function getQueryReturnsQueryIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\Cache::getQuery()
     * @expectedException Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryThrowsExceptionIfQueryDoesNotExist()
    {
        $this->loader->shouldReceive('getQuery')->once()->andThrow('Cocur\NQM\Exception\QueryNotExistsException');

        $this->cache->getQuery('foo');
    }
}
