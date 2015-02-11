<?php


namespace Cocur\NQM;


class QueryHelper
{
    /**
     * Prepends a colon to each parameter key.
     *
     * @param array $parameters
     *
     * @return array
     */
    public static function convertParameters(array $parameters)
    {
        $new = [];
        foreach ($parameters as $key => $value) {
            if (':' !== substr($key, 0, 1)) {
                $key = ':'.$key;
            }
            $new[$key] = $value;
        }

        return $new;
    }
}
