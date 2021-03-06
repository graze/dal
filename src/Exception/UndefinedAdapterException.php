<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Exception;

use Exception;
use OutOfRangeException;

class UndefinedAdapterException extends OutOfRangeException implements ExceptionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     * @param string $method
     * @param Exception $previous
     */
    public function __construct($name, $method = null, Exception $previous = null)
    {
        $this->name = $name;
        $message = sprintf('Entity "%s" does not have an Adapter. Check the adapter configuration.', $name);

        if ($method) {
            $message .= ' in ' . $method;
        }

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
