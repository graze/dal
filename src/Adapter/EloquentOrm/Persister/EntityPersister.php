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
namespace Graze\Dal\Adapter\EloquentOrm\Persister;

use Graze\Dal\Adapter\ActiveRecord\Persister\AbstractPersister;

/**
 * @deprecated - DAL 0.x
 */
class EntityPersister extends AbstractPersister
{
    /**
     * @param array $criteria
     * @param object $entity
     * @param array|null $orderBy
     *
     * @return null|object
     */
    public function load(array $criteria, $entity = null, array $orderBy = null)
    {
        $class = $this->recordName;
        $query = $class::query();

        foreach ($criteria as $field => $value) {
            $query->where($field, '=', $value);
        }

        if (is_null($orderBy)) {
            $orderBy = [];
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        $record = $query->first();

        if (! $record) {
            return null;
        }

        $mapper = $this->unitOfWork->getMapper($this->entityName);

        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function loadAll(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $class = $this->recordName;
        $query = $class::query();

        $query->limit($limit);
        if (! is_null($limit)) {
            $query->offset($offset);
        }

        foreach ($criteria as $field => $value) {
            $query->where($field, '=', $value);
        }

        if (is_null($orderBy)) {
            $orderBy = [];
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        $mapper = $this->unitOfWork->getMapper($this->entityName);

        return array_map(function ($record) use ($mapper) {
            $entity = $mapper->toEntity($record);
            $this->unitOfWork->setEntityRecord($entity, $record);

            return $this->persistImplicit($entity);
        }, $query->get()->all());
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return null|object
     */
    public function loadById($id, $entity = null)
    {
        $class = $this->recordName;
        $record = $class::find($id);

        if (is_null($record)) {
            return null;
        }

        $mapper = $this->unitOfWork->getMapper($this->entityName);

        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * @param object $entity
     */
    public function delete($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->entityName);
        $record = $this->unitOfWork->getEntityRecord($entity);
        $record = $mapper->fromEntity($entity, $record);

        $this->unitOfWork->setEntityRecord($entity, null);

        $record->delete();
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->entityName);
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
    public function save($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->entityName);
        $record = $this->unitOfWork->getEntityRecord($entity);
        $record = $mapper->fromEntity($entity, $record);

        $this->unitOfWork->setEntityRecord($entity, $record);

        $record->save();

        $this->unitOfWork->removeEntityRecord($entity);

        $mapper->toEntity($record, $entity);

        // set the entity record again after it's saved
        $this->unitOfWork->setEntityRecord($entity, $record);
    }

    /**
     * @param object $entity
     *
     * @return object
     */
    protected function persistImplicit($entity)
    {
        $this->unitOfWork->persistByTrackingPolicy($entity);

        return $entity;
    }
}
