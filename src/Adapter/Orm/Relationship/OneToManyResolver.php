<?php

namespace Graze\Dal\Adapter\Orm\Relationship;

use Graze\Dal\DalManagerInterface;
use Graze\Dal\Relationship\ResolverInterface;

class OneToManyResolver implements ResolverInterface
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
        $entity = $config['entity'];
        $repository = $this->dm->getRepository($entity);
        $entities = $repository->findBy([$config['foreignKey'] => $id]);

        return $entities;
    }
}
