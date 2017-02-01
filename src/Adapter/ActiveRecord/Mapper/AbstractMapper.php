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
namespace Graze\Dal\Adapter\ActiveRecord\Mapper;

use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * @deprecated - DAL 0.x
 */
abstract class AbstractMapper implements MapperInterface
{
    protected $entityName;
    protected $recordName;

    /**
     * @param string $entityName
     * @param string $recordName
     */
    public function __construct($entityName, $recordName)
    {
        $this->entityName = $entityName;
        $this->recordName = $recordName;
    }

    /**
     * @return HydratorInterface
     */
    abstract protected function getEntityHydrator();

    /**
     * @return HydratorInterface
     */
    abstract protected function getRecordHydrator();

    /**
     * @param object $entity
     *
     * @return array
     */
    public function getEntityData($entity)
    {
        return $this->getEntityHydrator()->extract($entity);
    }

    /**
     * @param object $record
     *
     * @return array
     */
    public function getRecordData($record)
    {
        return $this->getRecordHydrator()->extract($record);
    }
}
