<?php

namespace Graze\Dal\Test;

use Mockery;

trait MockTrait
{
    protected function getMockAdapter()
    {
        return Mockery::mock('Graze\Dal\Adapter\AdapterInterface');
    }

    protected function getMockUnitOfWork()
    {
        return Mockery::mock('Graze\Dal\UnitOfWork\UnitOfWorkInterface');
    }

    protected function getMockPersister()
    {
        return Mockery::mock('Graze\Dal\Persister\PersisterInterface');
    }

    protected function getMockEntity()
    {
        return Mockery::mock('Graze\Dal\Entity\EntityInterface');
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
