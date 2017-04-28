<?php

namespace Graze\Dal\Test\Unit\Relationship;

use Graze\Dal\Relationship\OneToManyResolver;
use Graze\Dal\Test\MockTrait;

class OneToManyResolverTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructedWithDalManager()
    {
        $resolver = new OneToManyResolver($this->getMockDalManager());
        static::assertInstanceOf(OneToManyResolver::class, $resolver);
    }

    public function testCanResolve()
    {
        $entity = $this->getMockEntity();

        $repository = $this->getMockRepository();
        $repository->shouldReceive('findBy')
            ->with(['foo_id' => 1])
            ->once()
            ->andReturn([$entity]);

        $dal = $this->getMockDalManager();
        $dal->shouldReceive('getRepository')
            ->with('Foo')
            ->once()
            ->andReturn($repository);

        $resolver = new OneToManyResolver($dal);
        $entities = $resolver->resolve('Bar', 'Foo', 1, ['foreignKey' => 'foo_id']);

        static::assertSame([$entity], $entities);
    }

    public function testCanResolveIfEntityNotFound()
    {
        $repository = $this->getMockRepository();
        $repository->shouldReceive('findBy')
            ->with(['foo_id' => 1])
            ->once()
            ->andReturn([]);

        $dal = $this->getMockDalManager();
        $dal->shouldReceive('getRepository')
            ->with('Foo')
            ->once()
            ->andReturn($repository);

        $resolver = new OneToManyResolver($dal);
        $entities = $resolver->resolve('Bar', 'Foo', 1, ['foreignKey' => 'foo_id']);

        static::assertSame([], $entities);
    }
}
