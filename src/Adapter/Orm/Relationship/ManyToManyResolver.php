<?php

namespace Graze\Dal\Adapter\Orm\Relationship;

use Graze\Dal\DalManagerInterface;
use Graze\Dal\Relationship\ResolverInterface;

class ManyToManyResolver implements ResolverInterface
{
    /**
     * @var DalManagerInterface
     */
    private $dm;

    /**
     * @param DalManagerInterface $dm
     */
    public function __construct(DalManagerInterface $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param string $entityName
     * @param int $id
     * @param array $config
     *
     * @return \Graze\Dal\Entity\EntityInterface[]
     */
    public function resolve($entityName, $id, array $config)
    {
        $foreignKey = $config['foreignKey'];
        $pivotTableName = $config['pivot'];
        $localKey = $config['localKey'];
        $foreignEntityName = $config['entity'];

        $sql = "SELECT {$foreignKey} FROM {$pivotTableName} WHERE {$localKey} = ?";

        // find all the $class entities using the many to many config
        $foreignRepository = $this->dm->getRepository($foreignEntityName);
        $localAdapter = $this->dm->findAdapterByEntityName($entityName);
        $foreignIds = array_values($localAdapter->fetchCol($sql, [$id]));

        $entities = [];
        foreach ($foreignIds as $id) {
            $entities[] = $foreignRepository->find($id);
        }

        return $entities;
    }
}
