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
namespace Graze\Dal\Adapter;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Exception\UndefinedRepositoryException;

interface AdapterInterface
{
    /**
     * @param string $class
     * @return ObjectRepository
     * @throws UndefinedRepositoryException If the repository is not found for name
     */
    public function getRepository($name);

    /**
     * @param string $class
     * @return boolean
     */
    public function hasRepository($name);

    /**
     * @param object $object
     */
    public function flush($object = null);

    /**
     * @param object $object
     */
    public function persist($object);

    /**
     * @param object $object
     */
    public function refresh($object);

    /**
     */
    public function beginTransaction();

    /**
     */
    public function commit();

    /**
     */
    public function rollback();
}
