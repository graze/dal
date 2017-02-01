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
namespace Graze\Dal\Exception;

use Exception;

class NotImplementedException extends \BadMethodCallException
{
    /**
     * @param string $class
     * @param int $method
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($class, $method, $code = 0, Exception $previous = null)
    {
        parent::__construct('Method ' . $method . ' is not implemented on class ' . $class, $code, $previous);
    }
}
