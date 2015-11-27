<?php

namespace Graze\Dal\Relationship;

class RelationshipResolver implements ResolverInterface
{
    /**
     * @var ManyToOneResolver
     */
    private $manyToOneResolver;

    /**
     * @var OneToManyResolver
     */
    private $oneToManyResolver;

    /**
     * @param ManyToOneResolver $manyToOneResolver
     * @param OneToManyResolver $oneToManyResolver
     */
    public function __construct(
        ManyToOneResolver $manyToOneResolver,
        OneToManyResolver $oneToManyResolver
    ) {
        $this->manyToOneResolver = $manyToOneResolver;
        $this->oneToManyResolver = $oneToManyResolver;
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

        switch ($type) {
            case 'manyToOne':
                return $this->manyToOneResolver->resolve($entityName, $id, $config);
            case 'oneToMany':
                return $this->oneToManyResolver->resolve($entityName, $id, $config);
        }
    }
}
