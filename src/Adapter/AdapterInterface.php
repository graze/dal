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
namespace Graze\Dal\Adapter;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

interface AdapterInterface
{
    /**
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity);

    /**
     * @param string $name
     *
     * @return ObjectRepository
     * @throws UndefinedRepositoryException If the repository is not found for name
     */
    public function getRepository($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRepository($name);

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
     * @return UnitOfWorkInterface
     */
    public function getUnitOfWork();

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration();
}
