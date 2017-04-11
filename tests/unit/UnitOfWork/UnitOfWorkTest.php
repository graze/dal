<?php

namespace Graze\Dal\Test\UnitOfWork;

use Graze\Dal\Identity\GeneratorInterface;
use Graze\Dal\Test\MockTrait;
use Graze\Dal\UnitOfWork\UnitOfWork;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    use MockTrait;

    public function testCanBeConstructed()
    {
        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $this->getMockConfig()
        );

        static::assertInstanceOf(UnitOfWork::class, $unitOfWork);
    }

    public function testCanCommitAll()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('save')->twice();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->persist($this->getMockEntity());
        $unitOfWork->persist($this->getMockEntity());

        $unitOfWork->commit();
    }

    public function testCanCommitTwiceSaveOnce()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('save')->twice();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->persist($this->getMockEntity());
        $unitOfWork->persist($this->getMockEntity());

        $unitOfWork->commit();
        $unitOfWork->commit();
    }

    public function testCanCommitEntity()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('save')->once();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $entityToCommit = $this->getMockEntity();

        $unitOfWork->persist($entityToCommit);
        $unitOfWork->persist($this->getMockEntity());

        $unitOfWork->commit($entityToCommit);
    }

    public function testCannotCommitUnPersistedEntity()
    {
        $persister = $this->getMockPersister();
        $persister->shouldNotReceive('save');

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $entityToCommit = $this->getMockEntity();

        $unitOfWork->persist($this->getMockEntity());

        $unitOfWork->commit($entityToCommit);
    }

    public function testCanPersistTwiceSaveOnce()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('save')->once();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $entityToCommit = $this->getMockEntity();

        $unitOfWork->persist($entityToCommit);
        $unitOfWork->persist($entityToCommit);

        $unitOfWork->commit();
    }

    public function testCanRefreshEntity()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('refresh')->once();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->refresh($this->getMockEntity());
    }

    public function testCanRemoveEntity()
    {
        $persister = $this->getMockPersister();
        $persister->shouldReceive('delete')->once();

        $config = $this->getMockConfig();
        $config->shouldReceive('buildPersister')
            ->andReturn($persister);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->remove($this->getMockEntity());
    }

    public function testPersistImplicitTrackingPolicy()
    {
        $config = $this->getMockConfig();

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->persistByTrackingPolicy($this->getMockEntity());
    }

    public function testPersistExplicitTrackingPolicy()
    {
        $config = $this->getMockConfig();

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_EXPLICIT
        );

        $unitOfWork->persistByTrackingPolicy($this->getMockEntity());
    }

    public function testCanGetEntityRecordNull()
    {
        $entity = $this->getMockEntity();

        $idGenerator = \Mockery::mock(GeneratorInterface::class);
        $idGenerator->shouldReceive('generate')
            ->with($entity)
            ->once()
            ->andReturn(uniqid());

        $config = $this->getMockConfig();
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($idGenerator);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        static::assertNull($unitOfWork->getEntityRecord($entity));
    }

    public function testCanGetEntityRecordNotNull()
    {
        $entity = $this->getMockEntity();
        $record = new \stdClass();

        $idGenerator = \Mockery::mock(GeneratorInterface::class);
        $idGenerator->shouldReceive('generate')
            ->with($entity)
            ->twice()
            ->andReturn(uniqid());

        $config = $this->getMockConfig();
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($idGenerator);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->setEntityRecord($entity, $record);

        static::assertSame($record, $unitOfWork->getEntityRecord($entity));
    }

    public function testCanGetEntityRecordDifferentEntities()
    {
        $entity1 = $this->getMockEntity();
        $entity2 = $this->getMockEntity();
        $record = new \stdClass();

        $idGenerator = \Mockery::mock(GeneratorInterface::class);
        $idGenerator->shouldReceive('generate')
            ->with($entity1)
            ->once()
            ->andReturn(rand());
        $idGenerator->shouldReceive('generate')
            ->withAnyArgs()
            ->once()
            ->andReturn(rand());

        $config = $this->getMockConfig();
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($idGenerator);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->setEntityRecord($entity2, $record);

        static::assertNull($unitOfWork->getEntityRecord($entity1));
    }

    public function testCanRemoveEntityRecord()
    {
        $entity = $this->getMockEntity();
        $record = new \stdClass();

        $idGenerator = \Mockery::mock(GeneratorInterface::class);
        $idGenerator->shouldReceive('generate')
            ->with($entity)
            ->times(3)
            ->andReturn(uniqid());

        $config = $this->getMockConfig();
        $config->shouldReceive('getIdentityGenerator')
            ->withNoArgs()
            ->andReturn($idGenerator);

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->setEntityRecord($entity, $record);
        $unitOfWork->removeEntityRecord($entity);

        self::assertNull($unitOfWork->getEntityRecord($entity));
    }

    public function testCanGetMapper()
    {
        $config = $this->getMockConfig();
        $config->shouldReceive('buildMapper')
            ->once()
            ->with('foo')
            ->andReturn($this->getMockMapper());

        $unitOfWork = new UnitOfWork(
            $this->getMockAdapter(),
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        $unitOfWork->getMapper('foo');
        $unitOfWork->getMapper('foo'); // call again to test run time caching
    }

    public function testCanGetAdapter()
    {
        $config = $this->getMockConfig();
        $adapter = $this->getMockAdapter();

        $unitOfWork = new UnitOfWork(
            $adapter,
            $config,
            UnitOfWork::POLICY_IMPLICIT
        );

        self::assertSame($adapter, $unitOfWork->getAdapter());
    }
}
