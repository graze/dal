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
namespace Graze\Dal\Adapter\EloquentOrm;

use Graze\Dal\Adapter\ActiveRecord\MapperInterface;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;
use ReflectionClass;

class EntityMapper implements MapperInterface
{
    protected $factory;
    protected $entityName;
    protected $recordName;
    protected $entityHydrator;
    protected $recordHydrator;
    protected $entityReflectionClass;
    protected $recordReflectionClass;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param HydratorFactory $factory
     */
    public function __construct($entityName, $recordName, HydratorFactory $factory)
    {
        $this->factory = $factory;
        $this->entityName = $entityName;
        $this->recordName = $recordName;
    }

    /**
     * {@inheritdoc}
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator()->extract($record);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $this->getRecordHydrator()->hydrate($data, $record);

        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function toEntity($record, $entity = null)
    {
        $data = $this->getRecordHydrator()->extract($record);
        $entity = is_object($entity) ? $entity : $this->instantiateEntity();

        $this->getEntityHydrator()->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @return HydratorInterface
     */
    protected function getEntityHydrator()
    {
        if (!$this->entityHydrator) {
            $this->entityHydrator = $this->factory->buildEntityHydrator($this->entityName);
        }

        return $this->entityHydrator;
    }

    /**
     * @return HydratorInterface
     */
    protected function getRecordHydrator()
    {
        if (!$this->recordHydrator) {
            $this->recordHydrator = $this->factory->buildRecordHydrator($this->recordName);
        }

        return $this->recordHydrator;
    }

    /**
     * @return object
     */
    protected function instantiateEntity()
    {
        if (!$this->entityReflectionClass) {
            $this->entityReflectionClass = new ReflectionClass($this->entityName);
        }

        return $this->entityReflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * @return object
     */
    protected function instantiateRecord()
    {
        if (!$this->recordReflectionClass) {
            $this->recordReflectionClass = new ReflectionClass($this->recordName);
        }

        return $this->recordReflectionClass->newInstanceWithoutConstructor();
    }
}
