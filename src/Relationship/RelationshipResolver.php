<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
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
     * @param string $localEntityName
     * @param string $foreignEntityName
     * @param int $id
     * @param array $config
     *
     * @return \Graze\Dal\Entity\EntityInterface[]
     */
    public function resolve($localEntityName, $foreignEntityName, $id, array $config)
    {
        $type = $config['type'];

        switch ($type) {
            case 'manyToMany':
                return $this->manyToManyResolver->resolve($localEntityName, $foreignEntityName, $id, $config);
            case 'manyToOne':
                return $this->manyToOneResolver->resolve($localEntityName, $foreignEntityName, $id, $config);
            case 'oneToMany':
                return $this->oneToManyResolver->resolve($localEntityName, $foreignEntityName, $id, $config);
        }
    }
}
