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

use ArrayAccess;
use Countable;
use PDOStatement;
use RuntimeException;

/**
 * StatementCollection
 *
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013-2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class StatementCollection implements Countable, ArrayAccess
{
    /**
     * @var PDOStatement[]
     */
    private $statements = [];

    /**
     * @param PDOStatement $statement
     *
     * @return StatementCollection
     */
    public function add(PDOStatement $statement)
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * @return PDOStatement[]
     */
    public function all()
    {
        return $this->statements;
    }

    /**
     * @return PDOStatement
     *
     * @throws RuntimeException if no statement is in the collection.
     */
    public function first()
    {
        if (!isset($this->statements[0])) {
            throw new RuntimeException('No statement exists in this collection, thus first cannot be returned.');
        }

        return $this->statements[0];
    }

    /**
     * @return PDOStatement
     *
     * @throws RuntimeException if no statement is in the collection.
     */
    public function last()
    {
        $count = $this->count();
        if (!$count) {
            throw new RuntimeException('No statement exists in this collection, thus last cannot be returned');
        }

        return $this->statements[$count-1];
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->statements);
    }

    /**
     * @param int $offset
     *
     * @return PDOStatement
     */
    public function offsetExists($offset)
    {
        return isset($this->statements[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return PDOStatement
     */
    public function offsetGet($offset)
    {
        return $this->statements[$offset];
    }

    /**
     * @param int          $offset
     * @param PDOStatement $value
     *
     * @throw RuntimeException Always, setting is not allowed
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('You cannot set a statement, please use add()');
    }

    /**
     * @params int $offset
     *
     * @throws RuntimeException Always, unsetting is not allowed.
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('You cannot unset a statement.');
    }
}
