<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\NamingStrategy;

/**
 * Naming Strategy use to replace underscore_case in Models with camelCase in Entities.
 */
class CamelCaseNamingStrategy implements NamingStrategyInterface
{
    /**
     * Convert the given name to underscore_case
     *
     * @param  string $name
     * @param  object|null $object
     *
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
     * @param  string $name
     * @param  array|null $data
     *
     * @return string
     */
    public function extract($name, $data = null)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    /**
     * @param string|object $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return true;
    }
}
