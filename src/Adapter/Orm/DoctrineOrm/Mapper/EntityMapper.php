<?php

namespace Graze\Dal\Adapter\Orm\DoctrineOrm\Mapper;

use Graze\Dal\Adapter\Orm\Mapper\AbstractMapper;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Hydrator\HydratorFactory;
use ReflectionClass;
use Zend\Stdlib\Hydrator\HydratorInterface;

class EntityMapper extends AbstractMapper
{
    /**
     * @var HydratorFactory
     */
    private $factory;

    /**
     * @var HydratorInterface
     */
    private $entityHydrator;

    /**
     * @var HydratorInterface
     */
    private $recordHydrator;

    private $entityReflectionClass;
    private $recordReflectionClass;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param HydratorFactory $factory
     */
    public function __construct($entityName, $recordName, HydratorFactory $factory)
    {
        parent::__construct($entityName, $recordName);
        $this->factory = $factory;
    }

    /**
     * @return HydratorInterface
     */
    protected function getEntityHydrator($entity)
    {
        if (! $this->entityHydrator) {
            $this->entityHydrator = $this->factory->buildEntityHydrator($entity);
        }

        return $this->entityHydrator;
    }

    /**
     * @return HydratorInterface
     */
    protected function getRecordHydrator($record)
    {
        if (! $this->recordHydrator) {
            $this->recordHydrator = $this->factory->buildRecordHydrator($record);
        }

        return $this->recordHydrator;
    }

    /**
     * @param object $entity
     * @param object $record
     *
     * @return object
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator($entity)->extract($entity);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $this->getRecordHydrator($record)->hydrate($data, $record);

        return $record;
    }

    /**
     * @param object $record
     * @param object $entity
     *
     * @return object
     */
    public function toEntity($record, $entity = null)
    {
        $data = $this->getRecordHydrator($record)->extract($record);
        $entity = is_object($entity) ? $entity : $this->instantiateEntity();

        $this->getEntityHydrator($entity)->hydrate($data, $entity);

        return $entity;
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
