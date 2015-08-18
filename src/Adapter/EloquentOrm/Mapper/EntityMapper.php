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
namespace Graze\Dal\Adapter\EloquentOrm\Mapper;

use Graze\Dal\Adapter\ActiveRecord\Mapper\AbstractMapper;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;
use ReflectionClass;
use Zend\Stdlib\Hydrator\HydratorInterface;

class EntityMapper extends AbstractMapper
{
    protected $factory;
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

        parent::__construct($entityName, $recordName);
    }

    /**
     * {@inheritdoc}
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator($entity)->extract($entity);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $this->getRecordHydrator($record)->hydrate($data, $record);

        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function toEntity($record, $entity = null)
    {
        $data = $this->getRecordHydrator($record)->extract($record);
        $entity = is_object($entity) ? $entity : $this->instantiateEntity();

        $this->getEntityHydrator($entity)->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @return HydratorInterface
     */
    protected function getEntityHydrator($entity)
    {
        if (!$this->entityHydrator) {
            $this->entityHydrator = $this->factory->buildEntityHydrator($entity);
        }

        return $this->entityHydrator;
    }

    /**
     * @return HydratorInterface
     */
    protected function getRecordHydrator($record)
    {
        if (!$this->recordHydrator) {
            $this->recordHydrator = $this->factory->buildRecordHydrator($record);
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
