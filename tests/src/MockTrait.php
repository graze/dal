<?php

namespace Graze\Dal\Test;

use Graze\Dal\Configuration\ConfigurationInterface;
use Mockery;

trait MockTrait
{
    protected function getMockAdapter()
    {
        return Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
    }

    protected function getMockManyToManyAdapter()
    {
        return Mockery::mock('Graze\Dal\Adapter\AdapterInterface,Graze\Dal\Relationship\ManyToManyInterface');
    }

    protected function getMockUnitOfWork()
    {
        return Mockery::mock('Graze\Dal\UnitOfWork\UnitOfWorkInterface');
    }

    protected function getMockMapper()
    {
        return Mockery::mock('Graze\Dal\Mapper\MapperInterface');
    }

    protected function getMockPersister()
    {
        return Mockery::mock('Graze\Dal\Persister\PersisterInterface');
    }

    protected function getMockEntity()
    {
        return Mockery::mock('Graze\Dal\Entity\EntityInterface');
    }

    protected function getMockEntityMetadata()
    {
        return Mockery::mock('Graze\Dal\Entity\EntityMetadata');
    }

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
