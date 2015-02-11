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

/**
 * QueryHelperTest
 *
 * @category  test
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013-2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class QueryHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Cocur\NQM\QueryHelper::convertParameters()
     */
    public function convertParametersAddsColonToParameter()
    {
        $params = QueryHelper::convertParameters(['foo' => 'bar', 'qoo' => 'baz']);

        $this->assertArrayHasKey(':foo', $params);
        $this->assertArrayHasKey(':qoo', $params);
        $this->assertArrayNotHasKey('foo', $params);
        $this->assertArrayNotHasKey('qoo', $params);
    }
}
