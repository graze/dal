<?php

namespace Graze\Dal\Adapter\DoctrineOrm\Persister;

use Graze\Dal\Adapter\ActiveRecord\Persister\AbstractPersister;

class EntityPersister extends AbstractPersister
{
    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    public function load(array $criteria, $entity = null, array $orderBy = null)
    {
        // TODO: Implement load() method.
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
        // TODO: Implement loadAll() method.
    }

    /**
     * @param array $criteria
     * @param object $entity
     *
     * @return object
     */
    public function loadById($id, $entity = null)
    {
        // TODO: Implement loadById() method.
    }

    /**
     * @param object $entity
     */
    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        // TODO: Implement refresh() method.
    }

    /**
     * @param object $entity
     */
    public function save($entity)
    {
        // TODO: Implement save() method.
    }
}
