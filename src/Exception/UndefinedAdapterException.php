<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
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
    protected $name;

    /**
     * @param string $name
     * @param string $method
     * @param Exception $previous
     */
    public function __construct($name, $method = null, Exception $previous = null)
    {
        $this->name = $name;
        $message = sprintf('Adapter "%s" is not defined', $name);

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
