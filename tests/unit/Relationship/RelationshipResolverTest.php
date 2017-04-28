<?php

namespace Graze\Dal\Test\Unit\Relationship;

use Graze\Dal\Relationship\ManyToManyResolver;
use Graze\Dal\Relationship\ManyToOneResolver;
use Graze\Dal\Relationship\OneToManyResolver;
use Graze\Dal\Relationship\RelationshipResolver;
use Mockery;

class RelationshipResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $resolver = new RelationshipResolver(
            $this->getMockManyToManyResolver(),
            $this->getMockManyToOneResolver(),
            $this->getMockOneToManyResolver()
        );

        static::assertInstanceOf(RelationshipResolver::class, $resolver);
    }

    public function testCanResolveManyToMany()
    {
        $manyToManyResolver = $this->getMockManyToManyResolver();
        $manyToManyResolver->shouldReceive('resolve')
            ->with('Foo', 'Bar', 1, ['type' => 'manyToMany'])
            ->once();

        $resolver = new RelationshipResolver(
            $manyToManyResolver,
            $this->getMockManyToOneResolver(),
            $this->getMockOneToManyResolver()
        );

        $resolver->resolve('Foo', 'Bar', 1, ['type' => 'manyToMany']);
    }

    public function testCanResolveManyToOne()
    {
        $manyToOneResolver = $this->getMockManyToOneResolver();
        $manyToOneResolver->shouldReceive('resolve')
            ->with('Foo', 'Bar', 1, ['type' => 'manyToOne'])
            ->once();

        $resolver = new RelationshipResolver(
            $this->getMockManyToManyResolver(),
            $manyToOneResolver,
            $this->getMockOneToManyResolver()
        );

        $resolver->resolve('Foo', 'Bar', 1, ['type' => 'manyToOne']);
    }

    public function testCanResolveOneToMany()
    {
        $oneToManyResolver = $this->getMockOneToManyResolver();
        $oneToManyResolver->shouldReceive('resolve')
            ->with('Foo', 'Bar', 1, ['type' => 'oneToMany'])
            ->once();

        $resolver = new RelationshipResolver(
            $this->getMockManyToManyResolver(),
            $this->getMockManyToOneResolver(),
            $oneToManyResolver
        );

        $resolver->resolve('Foo', 'Bar', 1, ['type' => 'oneToMany']);
    }

    public function testResolveUndefined()
    {
        $resolver = new RelationshipResolver(
            $this->getMockManyToManyResolver(),
            $this->getMockManyToOneResolver(),
            $this->getMockOneToManyResolver()
        );

        static::assertNull($resolver->resolve('Foo', 'Bar', 1, ['type' => 'fooBar']));
    }

    private function getMockManyToManyResolver()
    {
        return Mockery::mock(ManyToManyResolver::class);
    }

    private function getMockOneToManyResolver()
    {
        return Mockery::mock(OneToManyResolver::class);
    }

    private function getMockManyToOneResolver()
    {
        return Mockery::mock(ManyToOneResolver::class);
    }
}
