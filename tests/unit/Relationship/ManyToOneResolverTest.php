<?php

namespace Graze\Dal\Test\Unit\Relationship;

use Graze\Dal\Relationship\ManyToOneResolver;
use Graze\Dal\Test\MockTrait;

class ManyToOneResolverTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructedWithDalManager()
    {
        $resolver = new ManyToOneResolver($this->getMockDalManager());
        static::assertInstanceOf(ManyToOneResolver::class, $resolver);
    }

    public function testCanResolve()
    {
        $entity = $this->getMockEntity();

        $repository = $this->getMockRepository();
        $repository->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($entity);

        $dal = $this->getMockDalManager();
        $dal->shouldReceive('getRepository')
            ->with('Bar')
            ->once()
            ->andReturn($repository);

        $resolver = new ManyToOneResolver($dal);

        $entities = $resolver->resolve('Foo', 'Bar', 1, []);
        static::assertSame([$entity], $entities);
    }

    public function testCannotResolveEntityNotFound()
    {
        $repository = $this->getMockRepository();
        $repository->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturnNull();

        $dal = $this->getMockDalManager();
        $dal->shouldReceive('getRepository')
            ->with('Bar')
            ->once()
            ->andReturn($repository);

        $resolver = new ManyToOneResolver($dal);

        $entities = $resolver->resolve('Foo', 'Bar', 1, []);
        static::assertSame([null], $entities);
    }
}
