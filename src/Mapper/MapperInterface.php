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
namespace Graze\Dal\Mapper;

interface MapperInterface
{
    /**
     * @param object $entity
     *
     * @return array
     */
    public function getEntityData($entity);

    /**
     * @param object $record
     *
     * @return array
     */
    public function getRecordData($record);

    /**
     * @param object $entity
     * @param object $record
     *
     * @return object
     */
    public function fromEntity($entity, $record = null);

    /**
     * @param object $record
     * @param object $entity
     *
     * @return object
     */
    public function toEntity($record, $entity = null);
}
