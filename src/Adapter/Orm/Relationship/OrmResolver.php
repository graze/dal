<?php

namespace Graze\Dal\Adapter\Orm\Relationship;

use Graze\Dal\Relationship\ManyToOneResolver;
use Graze\Dal\Relationship\OneToManyResolver;
use Graze\Dal\Relationship\RelationshipResolver;

class OrmResolver extends RelationshipResolver
{
    /**
     * @var ManyToManyResolver
     */
    private $manyToManyResolver;

    /**
     * @param ManyToManyResolver $manyToManyResolver
     * @param ManyToOneResolver $manyToOneResolver
     * @param OneToManyResolver $oneToManyResolver
     */
    public function __construct(
        ManyToManyResolver $manyToManyResolver,
        ManyToOneResolver $manyToOneResolver,
        OneToManyResolver $oneToManyResolver
    ) {
        parent::__construct($manyToOneResolver, $oneToManyResolver);
        $this->manyToManyResolver = $manyToManyResolver;
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
        $type = $config['type'];

        if ('manyToMany' === $type) {
            return $this->manyToManyResolver->resolve($entityName, $id, $config);
        }

        return parent::resolve($entityName, $id, $config);
    }
}
