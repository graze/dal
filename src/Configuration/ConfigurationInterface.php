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
namespace Graze\Dal\Configuration;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Identity\GeneratorInterface;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Entity\EntityMetadata;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

interface ConfigurationInterface
{
    /**
     * @param string $name
     *
     * @return MapperInterface
     */
    public function buildMapper($name);

    /**
     * @param string $name
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return PersisterInterface
     */
    public function buildPersister($name, UnitOfWorkInterface $unitOfWork);

    /**
     * @param string $name
     * @param AdapterInterface $adapter
     *
     * @return ObjectRepository
     */
    public function buildRepository($name, AdapterInterface $adapter);

    /**
     * @param AdapterInterface $adapter
     *
     * @return UnitOfWorkInterface
     */
    public function buildUnitOfWork(AdapterInterface $adapter);

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getMapping($name);

    /**
     * @param EntityInterface $entity
     *
     * @return EntityMetadata
     */
    public function buildEntityMetadata(EntityInterface $entity);

    /**
     * @return GeneratorInterface
     */
    public function getIdentityGenerator();
}
