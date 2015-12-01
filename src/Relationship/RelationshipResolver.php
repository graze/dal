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
        $this->manyToOneResolver = $manyToOneResolver;
        $this->oneToManyResolver = $oneToManyResolver;
        $this->manyToManyResolver = $manyToManyResolver;
    }

    /**
     * @param string $entityName
     * @param int $id
     * @param array $config
     *
     * @return \Graze\Dal\Entity\EntityInterface[]
     * @throws \RuntimeException
     */
    public function resolve($entityName, $id, array $config)
    {
        $type = $config['type'];

        switch ($type) {
            case 'manyToMany':
                return $this->manyToManyResolver->resolve($entityName, $id, $config);
            case 'manyToOne':
                return $this->manyToOneResolver->resolve($entityName, $id, $config);
            case 'oneToMany':
                return $this->oneToManyResolver->resolve($entityName, $id, $config);
        }
    }
}
