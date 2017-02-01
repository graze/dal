<?php

namespace Graze\Dal\Relationship;

class ManyToOneResolver extends AbstractResolver implements ResolverInterface
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
        $entity = $repository->find($id);

        return [$entity];
    }
}
