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
