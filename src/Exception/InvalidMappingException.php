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
use InvalidArgumentException;

class InvalidMappingException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * @param string $message
     * @param string $method
     * @param Exception $previous
     */
    public function __construct($message, $method = null, Exception $previous = null)
    {
        if ($method) {
            $message .= ' in ' . $method;
        }

        parent::__construct($message, 0, $previous);
    }
}
