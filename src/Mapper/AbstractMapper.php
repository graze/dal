<?php

namespace Graze\Dal\Mapper;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Hydrator\Factory\HydratorFactoryInterface;
use ReflectionClass;
use Zend\Stdlib\Hydrator\HydratorInterface;

abstract class AbstractMapper implements MapperInterface
{
    /**
     * @var HydratorFactoryInterface
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

    /**
     * @var ReflectionClass
     */
    private $entityReflectionClass;

    /**
     * @var ReflectionClass
     */
    private $recordReflectionClass;

    /**
     * @var string
     */
    private $recordName;
    /**
     * @var string
     */
    private $entityName;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param HydratorFactoryInterface $factory
     * @param ConfigurationInterface $config
     */
    public function __construct(
        $entityName,
        $recordName,
        HydratorFactoryInterface $factory,
        ConfigurationInterface $config
    ) {
        $this->factory = $factory;
        $this->config = $config;
        $this->recordName = $recordName;
        $this->entityName = $entityName;
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
        if (class_exists($this->recordName)) {
            if (!$this->recordReflectionClass) {
                $this->recordReflectionClass = new ReflectionClass($this->recordName);
            }

            return $this->recordReflectionClass->newInstanceWithoutConstructor();
        }

        return ['_name' => $this->getRecordName()];
    }

    /**
     * @param object $entity
     *
     * @return array
     */
    public function getEntityData($entity)
    {
        return $this->getEntityHydrator($entity)->extract($entity);
    }

    /**
     * @param object $record
     *
     * @return array
     */
    public function getRecordData($record)
    {
        return $this->getRecordHydrator($record)->extract($record);
    }

    /**
     * @return string
     */
    protected function getRecordName()
    {
        return $this->recordName;
    }
}
