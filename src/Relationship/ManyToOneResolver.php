<?php

namespace Graze\Dal\Relationship;

use Graze\Dal\DalManagerInterface;

class ManyToOneResolver implements ResolverInterface
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
        $repository = $this->dm->getRepository($foreignEntityName);
        $entity = $repository->find($id);

        return [$entity];
    }
}