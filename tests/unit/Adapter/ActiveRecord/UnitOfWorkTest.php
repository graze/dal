<?php

namespace Graze\Dal\Test\Unit\Adapter\ActiveRecord;

use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Mockery;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructed()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $unitOfWork = new UnitOfWork($adapter, $config);
        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\UnitOfWork', $unitOfWork);
    }

    public function testCanCommitWithoutEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('save')
            ->with($entity)
            ->once();

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('getEntityName')
            ->with($entity)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('buildPersister')
            ->with('Graze\Dal\Test\Entity', $unitOfWork)
            ->andReturn($persister);

        $unitOfWork->persist($entity);
        $unitOfWork->commit();
    }

    public function testCanCommitWithEntity()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('save')
            ->with($entity)
            ->once();

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('getEntityName')
            ->with($entity)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('buildPersister')
            ->with('Graze\Dal\Test\Entity', $unitOfWork)
            ->andReturn($persister);

        $unitOfWork->persist($entity);
        $unitOfWork->commit($entity);
    }

    public function testCanPersistEntity()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $unitOfWork = new UnitOfWork($adapter, $config);
        $unitOfWork->persist(Mockery::mock('Graze\Dal\Test\Entity'));
    }

    public function testCanRefreshEntity()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('refresh')
            ->with($entity)
            ->once();

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('getEntityName')
            ->with($entity)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('buildPersister')
            ->with('Graze\Dal\Test\Entity', $unitOfWork)
            ->andReturn($persister);

        $unitOfWork->refresh($entity);
    }

    public function testCanRemoveEntity()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $persister = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface');
        $persister->shouldReceive('delete')
            ->with($entity)
            ->once();

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('getEntityName')
            ->with($entity)
            ->andReturn('Graze\Dal\Test\Entity');
        $config->shouldReceive('buildPersister')
            ->with('Graze\Dal\Test\Entity', $unitOfWork)
            ->andReturn($persister);

        $unitOfWork->remove($entity);
    }

    public function testCanPersistByTrackingPolicy()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $unitOfWork = new UnitOfWork($adapter, $config);
        $unitOfWork->persistByTrackingPolicy(Mockery::mock('Graze\Dal\Test\Entity'));
    }

    public function testCanSetAndGetEntityRecord()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $record = Mockery::mock('Graze\Dal\Test\Record');
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $generator = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Identity\GeneratorInterface');
        $generator->shouldReceive('generate')
            ->with($entity)
            ->twice()
            ->andReturn('hash');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($generator);

        $unitOfWork = new UnitOfWork($adapter, $config);

        $unitOfWork->setEntityRecord($entity, $record);
        $record = $unitOfWork->getEntityRecord($entity);

        static::assertInstanceOf('Graze\Dal\Test\Record', $record);
    }

    public function testCanRemoveEntityRecord()
    {
        $entity = Mockery::mock('Graze\Dal\Test\Entity');
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');

        $generator = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Identity\GeneratorInterface');
        $generator->shouldReceive('generate')
            ->with($entity)
            ->once()
            ->andReturn('hash');

        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($generator);

        $unitOfWork = new UnitOfWork($adapter, $config);
        $unitOfWork->removeEntityRecord($entity);
    }

    public function testCanGetMapper()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('buildMapper')
            ->with('foo', $unitOfWork)
            ->andReturn(Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface'));

        static::assertInstanceOf('Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface', $unitOfWork->getMapper('foo'));
    }

    public function testCanGetPersister()
    {
        $adapter = Mockery::mock('Graze\Dal\Adapter\ActiveRecordAdapter');
        $config = Mockery::mock('Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface');

        $unitOfWork = new UnitOfWork($adapter, $config);

        $config->shouldReceive('buildPersister')
            ->with('foo', $unitOfWork)
            ->andReturn(Mockery::mock('Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface'));

        static::assertInstanceOf(
            'Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface',
            $unitOfWork->getPersister('foo')
        );
    }
}
