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
namespace Graze\Dal\Adapter\Orm\Persister;

use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\UnitOfWork;
use Graze\Dal\Entity\EntityInterface;

abstract class AbstractPersister implements PersisterInterface
{
    protected $entityName;
    protected $recordName;
    protected $unitOfWork;

    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     * @param ConfigurationInterface $config
     */
    public function __construct($entityName, $recordName, UnitOfWork $unitOfWork, ConfigurationInterface $config)
    {
        $this->entityName = $entityName;
        $this->recordName = $recordName;
        $this->unitOfWork = $unitOfWork;
        $this->config = $config;
    }

    /**
     * @param object $record
     */
    abstract protected function saveRecord($record);

    /**
     * @param object $record
     */
    abstract protected function deleteRecord($record);

    /**
     * @return int
     */
    abstract protected function getRecordId($record);

    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    abstract protected function loadRecord(array $criteria, $entity = null, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    abstract protected function loadAllRecords(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param int $id
     * @param object $entity
     *
     * @return object
     */
    abstract protected function loadRecordById($id, $entity = null);

    /**
     * {@inheritdoc}
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordName()
    {
        return $this->recordName;
    }

    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    public function load(array $criteria, $entity = null, array $orderBy = null)
    {
        $record = $this->loadRecord($criteria, $entity, $orderBy);

        if (! $record) {
            return null;
        }

        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return object[]
     */
    public function loadAll(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $records = $this->loadAllRecords($criteria, $orderBy, $limit, $offset);

        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $entities = [];

        foreach ($records as $record) {
            $entity = $mapper->toEntity($record);
            $this->unitOfWork->setEntityRecord($entity, $record);
            $entities[] = $this->persistImplicit($entity);
        }

        return $entities;
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return object
     */
    public function loadById($id, $entity = null)
    {
        $record = $this->loadRecordById($id, $entity);

        if (is_null($record)) {
            return $record;
        }

        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $entity = $mapper->toEntity($record, $entity);

        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $data = $mapper->getEntityData($entity);

        if (isset($data['id'])) {
            $this->loadById($data['id'], $entity);
        } else {
            $record = $this->unitOfWork->getEntityRecord($entity);
            $mapper->toEntity($record, $entity);
        }
    }

    /**
     * @param object $entity
     */
    public function delete($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $record = $this->unitOfWork->getEntityRecord($entity);
        $record = $mapper->fromEntity($entity, $record);

        $this->unitOfWork->setEntityRecord($entity, null);

        $this->deleteRecord($record);
    }

    /**
     * @param object $entity
     */
    public function save($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->getEntityName());
        $record = $this->unitOfWork->getEntityRecord($entity);
        $record = $mapper->fromEntity($entity, $record);

        $this->unitOfWork->setEntityRecord($entity, $record);

        $this->saveRecord($record);

        $this->unitOfWork->removeEntityRecord($entity);

        $this->saveRelationships($entity, $this->getRecordId($record));

        $mapper->toEntity($record, $entity);

        $this->unitOfWork->setEntityRecord($entity, $record);
    }

    /**
     * @param EntityInterface $entity
     * @param int $recordId
     */
    private function saveRelationships(EntityInterface $entity, $recordId)
    {
        $metadata = $this->config->buildEntityMetadata($entity);
        $data = $this->unitOfWork->getMapper($this->entityName)->getEntityData($entity);

        foreach ($data as $field => $value) {
            // remove any keys that aren't relationships
            if (! $metadata->hasRelationship($field)) {
                unset($data[$field]);
            }
        }

        foreach ($data as $field => $value) {
            $relationship = $metadata->getRelationshipMetadata()[$field];

            if ('manyToMany' === $relationship['type']) {
                $table = $relationship['pivot'];
                // assume $value is a collection for manyToMany
                foreach ($value as $relatedEntity) {
                    // insert into $relationship['pivot'] ($relationship['localKey'], $relationship['foreignKey']) values ($entity->getId(), $relatedEntity->getId())
                    $data = [
                        $relationship['localKey'] => $recordId,
                        $relationship['foreignKey'] => $relatedEntity->getId(),
                    ];
                    $adapter = $this->unitOfWork->getAdapter();
                    $adapter->insert($table, $data);
                }
            }
        }
    }

    /**
     * @param object $entity
     * @return object
     */
    protected function persistImplicit($entity)
    {
        $this->unitOfWork->persistByTrackingPolicy($entity);

        return $entity;
    }
}
