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
use InvalidArgumentException;

class InvalidEntityException extends InvalidArgumentException implements ExceptionInterface
{
    protected $entity;

    /**
     * @param mixed $entity
     * @param string $method
     * @param Exception $previous
     */
    public function __construct($entity, $method = null, Exception $previous = null)
    {
        $this->entity = $entity;

        $representation = is_object($entity) ? get_class($entity) : (string) $entity;
        $message = sprintf('The value "%s" is not a valid entity', $representation);

        if ($method) {
            $message .= ' in ' . $method;
        }

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
