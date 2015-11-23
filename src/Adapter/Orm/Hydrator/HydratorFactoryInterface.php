<?php

namespace Graze\Dal\Adapter\Orm\Hydrator;

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
     * @param object $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record);
}
