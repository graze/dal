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

class EntityMapper extends AbstractMapper implements MapperInterface
{
    /**
     * @param object $entity
     * @param object $record
     *
     * @return object
     */
    public function fromEntity($entity, $record = null)
    {
        $data = $this->getEntityHydrator($entity)->extract($entity);
        $record = is_object($record) ? $record : $this->instantiateRecord();

        $metadata = $this->config->buildEntityMetadata($entity);
        foreach ($data as $field => $value) {
            if ($metadata->hasRelationship($field)) {
                unset($data[$field]);
            }
        }

        $record = $this->getRecordHydrator($record)->hydrate($data, $record);

        return $record;
    }

    /**
     * @param object $record
     * @param object $entity
     *
     * @return object
     */
    public function toEntity($record, $entity = null)
    {
        $data = $this->getRecordHydrator($record)->extract($record);
        $entity = is_object($entity) ? $entity : $this->instantiateEntity();

        $entity = $this->getEntityHydrator($entity)->hydrate($data, $entity);

        return $entity;
    }
}
