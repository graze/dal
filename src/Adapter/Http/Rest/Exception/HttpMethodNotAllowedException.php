<?php

namespace Graze\Dal\Adapter\Http\Rest\Exception;

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
