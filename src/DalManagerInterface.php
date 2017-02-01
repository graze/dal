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
namespace Graze\Dal;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Exception\UndefinedAdapterException;
use Graze\Dal\Exception\UndefinedRepositoryException;

interface DalManagerInterface
{
    /**
     * @param string $name
     *
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with name
     */
    public function get($name);

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function has($name);

    /**
     * @param AdapterInterface $adapter
     */
    public function set($name, AdapterInterface $adapter);

    /**
     * @return AdapterInterface[]
     */
    public function all();

    /**
     * @param string $name
     *
     * @return ObjectRepository
     * @throws UndefinedRepositoryException If the repository is not found for name
     */
    public function getRepository($name);

    /**
     * @param object $entity
     */
    public function flush($entity = null);

    /**
     * @param object $entity
     */
    public function persist($entity);

    /**
     * @param object $entity
     */
    public function refresh($entity);

    /**
     * @param object $entity
     */
    public function remove($entity);

    /**
     * @param string $name
     * @param callable $fn
     */
    public function transaction($name, callable $fn);

    /**
     * @param string $name
     *
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with name
     */
    public function findAdapterByEntityName($name);

    /**
     * @param object $entity
     *
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with entity
     */
    public function findAdapterByEntity($entity);
}
