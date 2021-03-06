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
namespace Graze\Dal\Adapter\EloquentOrm\Mapper;

use Graze\Dal\Adapter\ActiveRecord\Mapper\AbstractMapper;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;
use ReflectionClass;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * @deprecated - DAL 0.x
 */
class EntityMapper extends AbstractMapper
{
    /**
     * @var HydratorFactory
     */
    protected $factory;

    /**
     * @var HydratorInterface
     */
    protected $entityHydrator;

    /**
     * @var HydratorInterface
     */
    protected $recordHydrator;

    /**
     * @var ReflectionClass
     */
    protected $entityReflectionClass;

    /**
     * @var ReflectionClass
     */
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
     * @param object $entity
     * @param object $record
     *
     * @return null|object
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator()->extract($entity);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $this->getRecordHydrator()->hydrate($data, $record);

        return $record;
    }

    /**
     * @param object $record
     * @param object $entity
     *
     * @return null|object
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
        if (! $this->entityHydrator) {
            $this->entityHydrator = $this->factory->buildEntityHydrator($this->entityName);
        }

        return $this->entityHydrator;
    }

    /**
     * @return HydratorInterface
     */
    protected function getRecordHydrator()
    {
        if (! $this->recordHydrator) {
            $this->recordHydrator = $this->factory->buildRecordHydrator($this->recordName);
        }

        return $this->recordHydrator;
    }

    /**
     * @return object
     */
    protected function instantiateEntity()
    {
        if (! $this->entityReflectionClass) {
            $this->entityReflectionClass = new ReflectionClass($this->entityName);
        }

        return $this->entityReflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * @return object
     */
    protected function instantiateRecord()
    {
        if (! $this->recordReflectionClass) {
            $this->recordReflectionClass = new ReflectionClass($this->recordName);
        }

        return $this->recordReflectionClass->newInstanceWithoutConstructor();
    }
}
