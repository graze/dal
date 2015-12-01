<?php

namespace Graze\Dal\Relationship;

use Graze\Dal\DalManagerInterface;

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
