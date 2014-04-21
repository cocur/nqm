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

use Cocur\NQM\Exception\QueryNotExistsException;

/**
 * QueryLoader
 *
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class QueryLoader
{
    /** @var string */
    private $rootDir;

    /**
     * Constructor.
     *
     * @param string $rootDir The root directory.
     */
    public function __construct($rootDir = null)
    {
        if (null !== $rootDir) {
            $this->setRootDir($rootDir);
        }
    }

    /**
     * Sets the root directory.
     *
     * @param string $rootDir The root directory.
     *
     * @return QueryLoader
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;

        return $this;
    }

    /**
     * Returns the root directory.
     *
     * @return string The root directory.
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * Returns whether a query with the given name exists.
     *
     * @param string $name Name of a query.
     *
     * @return boolean `true` if the query exists, `false` otherwise.
     */
    public function hasQuery($name)
    {
        $filename = $this->getQueryFilename($name);

        return true === file_exists($filename) && true === is_file($filename) && true === is_readable($filename);
    }

    /**
     * Returns the query with the given name.
     *
     * @param string $name Name of a query.
     *
     * @return string SQL code of the query with the given name.
     *
     * @throws QueryNotExistsException if no query with the given name exists.
     */
    public function getQuery($name)
    {
        if (false === $this->hasQuery($name)) {
            throw new QueryNotExistsException(sprintf('There exists no query with the name "%s".', $name));
        }

        return file_get_contents($this->getQueryFilename($name));
    }

    /**
     * Returns the filename for the given name.
     *
     * @param string $name Name of a query.
     *
     * @return string Filename of the query.
     */
    protected function getQueryFilename($name)
    {
        return sprintf('%s/%s.sql', $this->rootDir, $name);
    }
}
