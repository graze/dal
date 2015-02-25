<?php

namespace Graze\Dal\NamingStrategy;

/**
 * Naming Strategy use to replace underscore_case in Models with camelCase in Entities.
 */
class CamelCaseNamingStrategy implements NamingStrategyInterface
{
    /**
     * Convert the given name to underscore_case
     *
     * @param  string      $name
     * @param  object|null $object
     * @return string
     */
    public function hydrate($name, $object = null)
    {
        return strtolower(
            preg_replace_callback(
                '/(?<!_)[A-Z]/',
                function ($match) {
                    return "_" . strtolower($match[0]);
                },
                lcfirst($name)
            )
        );
    }

    /**
     * Convert the given name to camelCase
     *
     * @param  string     $name
     * @param  array|null $data
     * @return string
     */
    public function extract($name, $data = null)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    /**
     * @param object $record
     *
     * @return bool
     */
    public function supports($record)
    {
        return true;
    }
}
