<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Orm\EloquentOrm;

use Graze\Dal\Adapter\Orm\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\UnitOfWork;
use Graze\Dal\Adapter\Orm\EloquentOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\EloquentOrm\Persister\EntityPersister;

class Configuration extends AbstractConfiguration
{
    private $hydratorFactory;

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        return new EntityPersister($entityName, $recordName, $unitOfWork, $this);
    }

    /**
     * @param UnitOfWork $unitOfWork
     *
     * @return HydratorFactory
     */
    protected function getHydratorFactory(UnitOfWork $unitOfWork)
    {
        if (! $this->hydratorFactory) {
            $proxyFactory = $this->buildProxyFactory($this->proxyConfiguration, $unitOfWork);
            $this->hydratorFactory = new HydratorFactory($this, $proxyFactory);
        }

        return $this->hydratorFactory;
    }
}
