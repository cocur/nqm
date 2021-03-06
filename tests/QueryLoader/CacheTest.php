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

use Cocur\NQM\QueryLoader\CacheQueryLoaderQueryLoader;
use Mockery as m;

/**
 * CacheTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Cocur\NQM\QueryLoader\QueryLoaderInterface|\Mockery\MockInterface */
    private $loader;

    /** @var CacheQueryLoader */
    private $cache;

    public function setUp()
    {
        /** @var \Cocur\NQM\QueryLoader\QueryLoaderInterface $loader */
        $loader = $this->loader = m::mock('Cocur\NQM\QueryLoader\QueryLoaderInterface');

        $this->cache = new CacheQueryLoader($loader);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::__construct()
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::getLoader()
     */
    public function getLoader()
    {
        $this->assertEquals($this->loader, $this->cache->getLoader());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::hasQuery()
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
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::hasQuery()
     */
    public function hasQueryReturnsTrueIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(true);

        $this->assertTrue($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::hasQuery()
     */
    public function hasQueryReturnsFalseIfQueryIsNotCachedAndNotExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(false);

        $this->assertFalse($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::getQuery()
     */
    public function getQueryReturnsQueryIfQueryIsCached()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');
        $this->cache->getQuery('foo'); // load query in cache

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::getQuery()
     */
    public function getQueryReturnsQueryIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\CacheQueryLoader::getQuery()
     * @expectedException \Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryThrowsExceptionIfQueryDoesNotExist()
    {
        $this->loader->shouldReceive('getQuery')->once()->andThrow('Cocur\NQM\Exception\QueryNotExistsException');

        $this->cache->getQuery('foo');
    }
}
