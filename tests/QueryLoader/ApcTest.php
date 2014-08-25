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

use Cocur\NQM\QueryLoader\ApcQueryLoaderQueryLoader;
use Mockery as m;

/**
 * ApcTest
 *
 * Attention: These tests are skipped on Travis CI.
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class ApcTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Cocur\NQM\QueryLoader\QueryLoaderInterface|\Mockery\MockInterface */
    private $loader;

    /** @var ApcQueryLoader */
    private $cache;

    public function setUp()
    {
        if (!function_exists('apc_clear_cache')) {
            $this->markTestSkipped('APC extension is not installed.');

            return;
        }

        if (!apc_store('nqm_test_foo', 'foobar') || apc_fetch('nqm_test_foo') !== 'foobar') {
            $this->markTestSkipped('APC extension is not working correctly.');
        }

        apc_clear_cache();
        /** @var \Cocur\NQM\QueryLoader\QueryLoaderInterface $loader */
        $loader = $this->loader = m::mock('Cocur\NQM\QueryLoader\QueryLoaderInterface');

        $this->cache = new ApcQueryLoader($loader, 'nqm_test.');
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::__construct()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getLoader()
     */
    public function getLoader()
    {
        $this->assertEquals($this->loader, $this->cache->getLoader());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcPrefix()
     */
    public function getApcPrefixReturnsApcPrefix()
    {
        $this->assertEquals('nqm_test.', $this->cache->getApcPrefix());
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::hasQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
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
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::hasQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
     */
    public function hasQueryReturnsTrueIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(true);

        $this->assertTrue($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::hasQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
     */
    public function hasQueryReturnsFalseIfQueryIsNotCachedAndNotExists()
    {
        $this->loader->shouldReceive('getQuery')->never();
        $this->loader->shouldReceive('hasQuery')->once()->andReturn(false);

        $this->assertFalse($this->cache->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
     */
    public function getQueryReturnsQueryIfQueryIsCached()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');
        $this->cache->getQuery('foo'); // load query in cache

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
     */
    public function getQueryReturnsQueryIfQueryIsNotCachedButExists()
    {
        $this->loader->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foo;');

        $this->assertEquals('SELECT * FROM foo;', $this->cache->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader\ApcQueryLoader::getApcName()
     * @expectedException \Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryThrowsExceptionIfQueryDoesNotExist()
    {
        $this->loader->shouldReceive('getQuery')->once()->andThrow('Cocur\NQM\Exception\QueryNotExistsException');

        $this->cache->getQuery('foo');
    }
}
