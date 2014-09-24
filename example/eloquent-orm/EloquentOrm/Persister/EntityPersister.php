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
namespace Graze\Dal\Adapter\EloquentOrm\Persister;

use Graze\Dal\Adapter\ActiveRecord\Persister\AbstractPersister;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use LogicException;

class EntityPersister extends AbstractPersister
{
    /**
     * {@inheritdoc}
     */
    public function load(array $criteria, $entity = null, array $orderBy = null)
    {
        $class = $this->recordName;
        $query = $class::query();

        foreach ($criteria as $field => $value) {
            $query->where($field, '=', $value);
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        $record = $query->first();
        $mapper = $this->unitOfWork->getMapper($this->entityName);

        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $class = $this->recordName;
        $query = $class::query();

        $query->limit($limit);
        $query->offset($limit);

        foreach ($criteria as $field => $value) {
            $query->where($field, '=', $value);
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        $mapper = $this->unitOfWork->getMapper($this->entityName);

        return array_map(function ($record) use ($mapper) {
            $mapper->toEntity($record);
            $this->unitOfWork->setEntityRecord($entity, $record);

            return $this->persistImplicit($entity);
        }, $query->get());
    }

    /**
     * {@inheritdoc}
     */
    public function loadById($id, $entity = null)
    {
        $class  = $this->recordName;
        $record = $class::find($id);
        $mapper = $this->unitOfWork->getMapper($this->entityName);

        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->setEntityRecord($entity, $record);

        return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function refresh($entity)
    {
        throw new LogicException('Entity refresh is not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->entityName);
        $record = $this->unitOfWork->getEntityRecord($entity);
        $record = $mapper->fromEntity($entity);

        $this->unitOfWork->setEntityRecord($entity, $record);

        $record->save();
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