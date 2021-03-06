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

class InvalidRepositoryException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var string
     */
    protected $repo;

    /**
     * @param mixed $repo
     * @param string $method
     * @param Exception $previous
     */
    public function __construct($repo, $method = null, Exception $previous = null)
    {
        $this->repo = $repo;

        $representation = is_object($repo) ? get_class($repo) : (string) $repo;
        $message = sprintf('The value "%s" is not an instance of ObjectRepository', $representation);

        if ($method) {
            $message .= ' in ' . $method;
        }

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repo;
    }
}
