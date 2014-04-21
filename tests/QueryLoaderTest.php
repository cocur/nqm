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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

use Cocur\NQM\QueryLoader;

/**
 * QueryLoaderTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class QueryLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var QueryLoader */
    private $loader;

    /** @var vfsStreamDirectory */
    private $rootDir;

    public function setUp()
    {
        $this->rootDir = new vfsStreamDirectory('queries');
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($this->rootDir);

        $this->loader = new QueryLoader($this->rootDir->url());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader::setRootDir()
     * @covers Cocur\NQM\QueryLoader::getRootDir()
     */
    public function setRootDirShouldSetRootDir()
    {
        $this->loader->setRootDir('./queries');
        $this->assertEquals('./queries', $this->loader->getRootDir());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader::__construct()
     */
    public function constructorShouldSetRootDir()
    {
        $loader = new QueryLoader('./queries');
        $this->assertEquals('./queries', $loader->getRootDir());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader::hasQuery()
     * @covers Cocur\NQM\QueryLoader::getQueryFilename()
     */
    public function hasQueryShouldReturnIfQueryExists()
    {
        $this->rootDir->addChild(new vfsStreamFile('foobar.sql'));

        $this->assertTrue($this->loader->hasQuery('foobar'));
        $this->assertFalse($this->loader->hasQuery('invalid'));
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader::getQueryFilename()
     */
    public function getQueryShouldReturnQuery()
    {
        $file = new vfsStreamFile('foobar.sql');
        $file->setContent('SELECT * FROM table;');
        $this->rootDir->addChild($file);

        $this->assertEquals('SELECT * FROM table;', $this->loader->getQuery('foobar'));
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader::getQueryFilename()
     *
     * @expectedException Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryShouldThrowExceptionIfQueryDoesNotExist()
    {
        $this->loader->getQuery('invalid');
    }
}
