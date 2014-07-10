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

use Graze\Dal\Adapter\ActiveRecord\PersisterInterface;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;

class EntityPersister implements PersisterInterface
{
    protected $entityName;
    protected $recordName;
    protected $unitOfWork;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     */
    public function __construct($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        $this->entityName = $entityName;
        $this->recordName = $recordName;
        $this->unitOfWork = $unitOfWork;
    }

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

        return $record ? $this->persistImplicit($mapper->toEntity($record, $entity)) : null;
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
            return $this->persistImplicit($mapper->toEntity($record));
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

        return $record ? $this->persistImplicit($mapper->toEntity($record, $entity)) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity)
    {
        $mapper = $this->unitOfWork->getMapper($this->entityName);
        $record = $mapper->fromEntity($entity);

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
