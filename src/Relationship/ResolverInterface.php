<?php

namespace Graze\Dal\Relationship;

interface ResolverInterface
{
    /**
     * @param string $entityName
     * @param int $id
     * @param array $config
     *
     * @return \Graze\Dal\Entity\EntityInterface[]
     */
    public function resolve($entityName, $id, array $config);
}
