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

use Cocur\NQM\QueryLoader\ArrayQueryLoader;
use Mockery as m;

/**
 * ArrayQueryLoaderTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013-2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class ArrayQueryLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayQueryLoader */
    private $loader;

    public function setUp()
    {
        $this->loader = new ArrayQueryLoader(['foo' => 'SELECT;']);
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ArrayQueryLoader::hasQuery()
     */
    public function hasQueryReturnsTrueIfQueryExists()
    {
        $this->assertTrue($this->loader->hasQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ArrayQueryLoader::hasQuery()
     */
    public function hasQueryReturnsFalseIfQueryDoesNotExist()
    {
        $this->assertFalse($this->loader->hasQuery('invalid'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ArrayQueryLoader::getQuery()
     */
    public function getQueryReturnsQueryIfQueryExists()
    {
        $this->assertEquals('SELECT;', $this->loader->getQuery('foo'));
    }

    /**
     * @test
     * @covers Cocur\NQM\QueryLoader\ArrayQueryLoader::getQuery()
     * @expectedException \Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryThrowsExceptionIfQueryDoesNotExist()
    {
        $this->loader->getQuery('invalid');
    }
}
