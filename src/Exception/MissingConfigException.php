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

class MissingConfigException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @param string $entityName
     * @param string $configField
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($entityName, $configField, $code = 0, \Exception $previous = null)
    {
        $message = "Missing config '%s' for entity %s";
        parent::__construct(sprintf($message, $configField, $entityName), $code, $previous);
    }
}
