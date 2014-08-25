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

use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * FilesystemTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemQueryLoader */
    private $loader;

    /** @var vfsStreamDirectory */
    private $rootDir;

    public function setUp()
    {
        $this->rootDir = new vfsStreamDirectory('queries');
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($this->rootDir);

        $this->loader = new FilesystemQueryLoader($this->rootDir->url());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::setRootDir()
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getRootDir()
     */
    public function setRootDirShouldSetRootDir()
    {
        $this->loader->setRootDir('./queries');
        $this->assertEquals('./queries', $this->loader->getRootDir());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::__construct()
     */
    public function constructorShouldSetRootDir()
    {
        $loader = new FilesystemQueryLoader('./queries');
        $this->assertEquals('./queries', $loader->getRootDir());
    }

    /**
     * @test
     *
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::hasQuery()
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getQueryFilename()
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
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getQueryFilename()
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
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getQuery()
     * @covers Cocur\NQM\QueryLoader\FilesystemQueryLoader::getQueryFilename()
     *
     * @expectedException \Cocur\NQM\Exception\QueryNotExistsException
     */
    public function getQueryShouldThrowExceptionIfQueryDoesNotExist()
    {
        $this->loader->getQuery('invalid');
    }
}
