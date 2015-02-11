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
 * NQMCollection
 *
 * @package   cocur/nqm
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2013-2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class NQMCollection
{
    /**
     * @var NQM
     */
    private $nqm;

    /**
     * @param NQM $nqm
     */
    public function __construct(NQM $nqm)
    {
        $this->nqm = $nqm;
    }

    /**
     * @param string $name Name of the query collection
     *
     * @return string[]
     */
    public function getQueries($name)
    {
        return array_map('trim', preg_split("/\n#;\n/", $this->nqm->getQuery($name)));
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return StatementCollection
     */
    public function prepare($name, $options = [])
    {
        $pdo = $this->nqm->getPdo();
        $statements = new StatementCollection();

        foreach ($this->getQueries($name) as $query) {
            $statements->add($pdo->prepare($query, $options));
        }

        return $statements;
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param array  $options
     *
     * @return StatementCollection
     */
    public function execute($name, $parameters = [], $options = [])
    {
        $pdo = $this->nqm->getPdo();
        $statements = new StatementCollection();

        foreach ($this->getQueries($name) as $index => $query) {
            $statement = $pdo->prepare($query, $options);
            foreach ($this->getQueryParameters($query, QueryHelper::convertParameters($parameters)) as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();
            $statements->add($statement);
        }

        return $statements;
    }

    /**
     * Returns all parameters from the given parameters array that occur in the given query.
     *
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    protected function getQueryParameters($query, array $parameters)
    {
        $queryParameters = [];
        foreach ($parameters as $key => $value) {
            if (preg_match('/'.$key.'/', $query)) {
                $queryParameters[$key] = $value;
            }
        }

        return $queryParameters;
    }
}
