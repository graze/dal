<?php

namespace Graze\Dal\Exception;

use Exception;
use Graze\Dal\Adapter\AdapterInterface;

class NotSupportedException extends \BadMethodCallException
{
    /**
     * @param string $message
     * @param AdapterInterface $adapter
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, AdapterInterface $adapter, $code = 0, Exception $previous = null)
    {
        parent::__construct(get_class($adapter) . ': ' . $message, $code, $previous);
    }
}
