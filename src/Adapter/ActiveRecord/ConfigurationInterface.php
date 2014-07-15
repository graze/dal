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
namespace Graze\Dal\Adapter\ActiveRecord;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\ActiveRecordAdapter;

interface ConfigurationInterface
{
    /**
     * @param string $name
     * @param UnitOfWork $unitOfWork
     * @return MapperInterface
     */
    public function buildMapper($name, UnitOfWork $unitOfWork);

    /**
     * @param string $name
     * @param UnitOfWork $unitOfWork
     * @return PersisterInterface
     */
    public function buildPersister($name, UnitOfWork $unitOfWork);

    /**
     * @param string $name
     * @param PersisterInterface $persister
     * @return ObjectRepository
     */
    public function buildRepository($name, ActiveRecordAdapter $adapter);

    /**
     * @param ActiveRecordAdapter $adapter
     * @return UnitOfWork
     */
    public function buildUnitOfWork(ActiveRecordAdapter $adapter);

    /**
     * @param object $entity
     * @return string
     */
    public function getEntityName($entity);

    /**
     * @param object $record
     * @return string
     */
    public function getEntityNameFromRecord($record);

    /**
     * @param string $name
     * @return array
     */
    public function getMapping($name);
}
