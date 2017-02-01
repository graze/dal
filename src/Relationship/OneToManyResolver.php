<?php

namespace Graze\Dal\Relationship;

class OneToManyResolver extends AbstractResolver implements ResolverInterface
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
        $repository = $this->dm->getRepository($foreignEntityName);
        $entities = $repository->findBy([$config['foreignKey'] => $id]);

        return $entities;
    }
}
