<?php

namespace Graze\Dal\Test\UnitOfWork;

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
}
