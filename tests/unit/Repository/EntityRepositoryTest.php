<?php

namespace Graze\Dal\Test\Unit\Repository;

use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\Repository\EntityRepository;
use Graze\Dal\Test\Entity;
use Graze\Dal\Test\MockTrait;
use Mockery;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructed()
    {
        $adapter = $this->getMockAdapter();
        $repository = new EntityRepository('entityName', $adapter);

        static::assertInstanceOf('Graze\Dal\Repository\EntityRepository', $repository);
    }

    public function testCanFindValidId()
    {
        $entity = $this->getMockEntity();

        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadById')
            ->with(1)
            ->andReturn($entity);

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertEquals($repository->find(1), $entity);
    }

    public function testFindReturnsNullForInvalidId()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadById')
            ->with(1)
            ->andReturnNull();

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertNull($repository->find(1));
    }

    public function testCanFindByValidCriteria()
    {
        $entity = $this->getMockEntity();
        $criteria = ['foo' => 'bar'];

        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadAll')
            ->with($criteria, null, null, null)
            ->andReturn([$entity]);

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertEquals($repository->findBy($criteria), [$entity]);
    }

    public function testFindByReturnsNullForInvalidCriteria()
    {
        $criteria = ['foo' => 'bar'];

        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadAll')
            ->with($criteria, null, null, null)
            ->andReturnNull();

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertNull($repository->findBy($criteria));
    }

    public function testCanFindAll()
    {
        $entity = $this->getMockEntity();

        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadAll')
            ->with([], null, null, null)
            ->andReturn([$entity]);

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertEquals($repository->findAll(), [$entity]);
    }

    public function testFindAllReturnsNull()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('loadAll')
            ->with([], null, null, null)
            ->andReturnNull();

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertNull($repository->findAll());
    }

    public function testCanFindOneByValidCriteria()
    {
        $entity = $this->getMockEntity();
        $criteria = ['foo' => 'bar'];

        $persister = $this->getMockPersister();
        $persister->shouldReceive('load')
            ->with($criteria, null, null)
            ->andReturn([$entity]);

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertEquals($repository->findOneBy($criteria), [$entity]);
    }

    public function testFindOneByReturnsNullForInvalidCriteria()
    {
        $criteria = ['foo' => 'bar'];

        $persister = $this->getMockPersister();
        $persister->shouldReceive('load')
            ->with($criteria, null, null)
            ->andReturnNull();

        $adapter = $this->getMockAdapterWithPersister($persister);

        $repository = new EntityRepository('entityName', $adapter);
        static::assertNull($repository->findOneBy($criteria));
    }

    public function testCanGetEntityName()
    {
        $repository = new EntityRepository('entityName', $this->getMockAdapter());
        static::assertEquals('entityName', $repository->getClassName());
    }

    private function getMockAdapterWithPersister(PersisterInterface $persister)
    {
        $unitOfWork = $this->getMockUnitOfWork();
        $unitOfWork->shouldReceive('getPersister')
            ->with('entityName')
            ->andReturn($persister);

        $adapter = $this->getMockAdapter();
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        return $adapter;
    }

}
