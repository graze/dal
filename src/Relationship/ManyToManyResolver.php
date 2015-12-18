<?php

namespace Graze\Dal\Relationship;

class ManyToManyResolver extends AbstractResolver implements ResolverInterface
{
    /**
     * @param string $localEntityName
     * @param string $foreignEntityName
     * @param int $id
     * @param array $config
     *
     * @return \Graze\Dal\Entity\EntityInterface[]
     */
    public function resolve($localEntityName, $foreignEntityName, $id, array $config)
    {
        $foreignKey = $config['foreignKey'];
        $pivotTableName = $config['pivot'];
        $localKey = $config['localKey'];

        $sql = "SELECT {$foreignKey} FROM {$pivotTableName} WHERE {$localKey} = ?";

        // find all the $class entities using the many to many config
        $foreignRepository = $this->dm->getRepository($foreignEntityName);
        $localAdapter = $this->dm->findAdapterByEntityName($localEntityName);

        if (! $localAdapter instanceof ManyToManyInterface) {
            throw new \RuntimeException('Adapter ' . get_class($localAdapter) . ' must implement ManyToManyInterface to support manyToMany relationships');
        }

        $foreignIds = array_values($localAdapter->fetchCol($sql, [$id]));

        $entities = [];
        foreach ($foreignIds as $id) {
            $entities[] = $foreignRepository->find($id);
        }

        return $entities;
    }
}
