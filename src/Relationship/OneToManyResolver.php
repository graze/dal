<?php

namespace Graze\Dal\Relationship;

use Graze\Dal\DalManagerInterface;

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
