<?php

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
