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
 * Naming Strategy used when hydrating/exteracting fields.
 * This will add the prefix when dealing with the model
 *
 * @deprecated - DAL 0.x
 */
class PrefixNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Create the naming strategy
     *
     * @param string $prefix The prefix to use
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Convert the given name into a prefixed name
     *
     * @param  string $name The original name
     * @param  object $object (optional) The original object for context.
     *
     * @return string           The name with a prefix attached.
     */
    public function hydrate($name, $object = null)
    {
        return $this->prefix . $name;
    }

    /**
     * Remove any prefixes if applicable
     *
     * @param  string $name The original name
     * @param  array|null $data (optional) The original data for context
     *
     * @return string       The extracted name
     */
    public function extract($name, $data = null)
    {
        if (substr($name, 0, strlen($this->prefix)) == $this->prefix) {
            $newname = substr($name, strlen($this->prefix));
        } else {
            $newname = $name;
        }
        return $newname;
    }

    /**
     * @param string|object $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return is_subclass_of($object, 'Graze\Dal\NamingStrategy\ColumnPrefixInterface');
    }
}
