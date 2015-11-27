<?php

namespace Graze\Dal\Adapter\Orm\Relationship;

use Graze\Dal\Relationship\ResolverInterface;

class OrmResolver implements ResolverInterface
{
    /**
     * @var ManyToManyResolver
     */
    private $manyToManyResolver;

    /**
     * @var ManyToOneResolver
     */
    private $manyToOneResolver;

    /**
     * @var OneToManyResolver
     */
    private $oneToManyResolver;

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
        $this->manyToManyResolver = $manyToManyResolver;
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
            case 'manyToMany':
                return $this->manyToManyResolver->resolve($entityName, $id, $config);
            case 'manyToOne':
                return $this->manyToOneResolver->resolve($entityName, $id, $config);
            case 'oneToMany':
                return $this->oneToManyResolver->resolve($entityName, $id, $config);
        }
    }
}
