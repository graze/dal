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
namespace Graze\Dal\Adapter\Orm\EloquentOrm\Persister;

use Graze\Dal\Persister\AbstractPersister;

class EntityPersister extends AbstractPersister
{
    /**
     * @param object $record
     *
     * @return array|object
     */
    protected function saveRecord($record)
    {
        $record->save();
        return $record;
    }

    /**
     * @param object $record
     */
    protected function deleteRecord($record)
    {
        $record->delete();
    }

    /**
     * @param object $record
     *
     * @return int
     */
    protected function getRecordId($record)
    {
        return $record->id;
    }

    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    protected function loadRecord(array $criteria, $entity = null, array $orderBy = null)
    {
        $class = $this->getRecordName();
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

        return $query->first();
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    protected function loadAllRecords(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $class = $this->getRecordName();
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

        return $query->get()->all();
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return object
     */
    protected function loadRecordById($id, $entity = null)
    {
        $class = $this->getRecordName();
        return $class::find($id);
    }
}
