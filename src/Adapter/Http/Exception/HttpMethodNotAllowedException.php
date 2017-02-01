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
namespace Graze\Dal\Adapter\Http\Exception;

use Exception;

class HttpMethodNotAllowedException extends \InvalidArgumentException
{
    /**
     * @param string $method
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($method, $code = 0, Exception $previous = null)
    {
        parent::__construct("HTTP method {$method} is not allowed for this entity. Check entity configuration.", $code, $previous);
    }
}
