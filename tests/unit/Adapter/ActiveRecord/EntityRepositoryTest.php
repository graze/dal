<?php

namespace Graze\Dal\Test\Unit\Adapter\ActiveRecord;

use Graze\Dal\Adapter\ActiveRecord\EntityRepository;
use Mockery;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $repository = new EntityRepository('foo', $adapter);

        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\EntityRepository', $repository);
        static::assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $repository);
    }

    public function testCanFindById()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('loadById')
            ->with(1)
            ->once()
            ->andReturn($entity);

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);
        static::assertEquals($repository->find(1), $entity);
    }

    public function testCanFindAll()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('loadAll')
            ->with([], null, null, null)
            ->once()
            ->andReturn([$entity]);

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);

        static::assertEquals($repository->findAll(), [$entity]);
    }

    public function testCanFindByCriteria()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('loadAll')
            ->with(['foo' => 'bar'], null, null, null)
            ->once()
            ->andReturn([$entity]);

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);

        static::assertEquals($repository->findBy(['foo' => 'bar']), [$entity]);
    }

    public function testCanFindByCriteriaNoResults()
    {
        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('loadAll')
            ->with(['foo' => 'bar'], null, null, null)
            ->once()
            ->andReturn([]);

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);

        static::assertEmpty($repository->findBy(['foo' => 'bar']));
    }

    public function testCanFindOneByCriteria()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('load')
            ->with(['foo' => 'bar'], null, null)
            ->once()
            ->andReturn($entity);

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);

        static::assertEquals($repository->findOneBy(['foo' => 'bar']), $entity);
    }

    public function testCanFindOneByCriteriaNoResults()
    {
        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('load')
            ->with(['foo' => 'bar'], null, null)
            ->once()
            ->andReturnNull();

        $unitOfWork = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\UnitOfWork');
        $unitOfWork->shouldReceive('getPersister')
            ->with('foo')
            ->andReturn($persister);

        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $adapter->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->andReturn($unitOfWork);

        $repository = new EntityRepository('foo', $adapter);

        static::assertEmpty($repository->findOneBy(['foo' => 'bar']));
    }

    public function testCanGetClassName()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $repository = new EntityRepository('foo', $adapter);

        static::assertInternalType('string', $repository->getClassName());
        static::assertEquals('foo', $repository->getClassName());
    }
}
