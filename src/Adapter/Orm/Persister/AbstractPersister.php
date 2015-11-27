<?php

namespace Graze\Dal\Adapter\Orm\Persister;

use Graze\Dal\Persister\PersisterInterface;

abstract class AbstractPersister extends \Graze\Dal\Persister\AbstractPersister implements PersisterInterface
{
    /**
     * @param object $entity
     * @param array|object $record
     * @todo - refactor this madness
     */
    protected function postSaveHook($entity, $record)
    {
        $metadata = $this->config->buildEntityMetadata($entity);
        $data = $this->unitOfWork->getMapper($this->entityName)->getEntityData($entity);
        $recordId = $this->getRecordId($record);

        foreach ($data as $field => $value) {
            // remove any keys that aren't relationships
            if (! $metadata->hasRelationship($field)) {
                unset($data[$field]);
            }
        }

        foreach ($data as $field => $value) {
            $relationship = $metadata->getRelationshipMetadata()[$field];

            if ('manyToMany' === $relationship['type']) {
                $table = $relationship['pivot'];
                // assume $value is a collection for manyToMany
                foreach ($value as $relatedEntity) {
                    // insert into $relationship['pivot'] ($relationship['localKey'], $relationship['foreignKey']) values ($entity->getId(), $relatedEntity->getId())
                    $data = [
                        $relationship['localKey'] => $recordId,
                        $relationship['foreignKey'] => $relatedEntity->getId(),
                    ];
                    $adapter = $this->unitOfWork->getAdapter();
                    $adapter->insert($table, $data);
                }
            }
        }
    }
}
