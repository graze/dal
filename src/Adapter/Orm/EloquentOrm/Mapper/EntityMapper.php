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
namespace Graze\Dal\Adapter\Orm\EloquentOrm\Mapper;

use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Mapper\AbstractMapper;
use Graze\Dal\Adapter\Orm\EloquentOrm\Hydrator\HydratorFactory;
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
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param HydratorFactory $factory
     * @param ConfigurationInterface $config
     */
    public function __construct($entityName, $recordName, HydratorFactory $factory, ConfigurationInterface $config)
    {
        $this->factory = $factory;
        $this->config = $config;

        parent::__construct($entityName, $recordName);
    }

    /**
     * {@inheritdoc}
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator($entity)->extract($entity);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $metadata = $this->config->buildEntityMetadata($entity);
        foreach ($data as $field => $value) {
            if ($metadata->hasRelationship($field)) {
                unset($data[$field]);
            }
        }

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
