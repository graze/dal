<?php

namespace Graze\Dal\Test;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Entity\EntityMetadata;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\Relationship\ManyToManyInterface;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use Mockery;

trait MockTrait
{
    /**
     * @return \Mockery\MockInterface|AdapterInterface
     */
    protected function getMockAdapter()
    {
        return Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
    }

    /**
     * @return \Mockery\MockInterface|ManyToManyInterface
     */
    protected function getMockManyToManyAdapter()
    {
        return Mockery::mock('Graze\Dal\Adapter\AdapterInterface,Graze\Dal\Relationship\ManyToManyInterface');
    }

    /**
     * @return \Mockery\MockInterface|UnitOfWorkInterface
     */
    protected function getMockUnitOfWork()
    {
        return Mockery::mock('Graze\Dal\UnitOfWork\UnitOfWorkInterface');
    }

    /**
     * @return \Mockery\MockInterface|MapperInterface
     */
    protected function getMockMapper()
    {
        return Mockery::mock('Graze\Dal\Mapper\MapperInterface');
    }

    /**
     * @return \Mockery\MockInterface|PersisterInterface
     */
    protected function getMockPersister()
    {
        return Mockery::mock('Graze\Dal\Persister\PersisterInterface');
    }

    /**
     * @return \Mockery\MockInterface|EntityInterface
     */
    protected function getMockEntity()
    {
        return Mockery::mock('Graze\Dal\Entity\EntityInterface');
    }

    /**
     * @return \Mockery\MockInterface|EntityMetadata
     */
    protected function getMockEntityMetadata()
    {
        return Mockery::mock('Graze\Dal\Entity\EntityMetadata');
    }

    /**
     * @return \Mockery\MockInterface|ConfigurationInterface
     */
    protected function getMockConfig()
    {
        $config = Mockery::mock('Graze\Dal\Configuration\ConfigurationInterface');
        $config->shouldReceive('getEntityName')
            ->andReturnUsing(function ($entity) {
                return get_class($entity);
            });

        return $config;
    }
}
