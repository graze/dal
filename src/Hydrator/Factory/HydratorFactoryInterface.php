<?php

namespace Graze\Dal\Hydrator\Factory;

use Zend\Stdlib\Hydrator\HydratorInterface;

interface HydratorFactoryInterface
{
    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entity);

    /**
     * @param object|array $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record);
}
