<?php

namespace Graze\Dal\UnitOfWork;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Persister\PersisterInterface;

interface UnitOfWorkInterface
{
    /**
     * @param object $entity
     */
    public function commit($entity = null);

    /**
     * @param object $entity
     */
    public function persist($entity);

    /**
     * @param object $entity
     */
    public function refresh($entity);

    /**
     * @param object $entity
     */
    public function remove($entity);

    /**
     * @param object $entity
     */
    public function persistByTrackingPolicy($entity);

    /**
     * @param object $entity
     *
     * @return object
     */
    public function getEntityRecord($entity);

    /**
     * @param object $entity
     * @param object $record
     */
    public function setEntityRecord($entity, $record);

    /**
     * @param object $entity
     */
    public function removeEntityRecord($entity);

    /**
     * @param string $name
     *
     * @return MapperInterface
     */
    public function getMapper($name);

    /**
     * @param string $name
     *
     * @return PersisterInterface
     */
    public function getPersister($name);

    /**
     * @return AdapterInterface
     */
    public function getAdapter();
}
