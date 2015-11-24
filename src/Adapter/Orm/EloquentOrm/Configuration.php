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

use Graze\Dal\Adapter\Orm\Configuration\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\EloquentOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\EloquentOrm\Persister\EntityPersister;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactoryInterface;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class Configuration extends AbstractConfiguration
{
    /**
     * @var HydratorFactoryInterface
     */
    private $hydratorFactory;

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWorkInterface $unitOfWork)
    {
        return new EntityPersister($entityName, $recordName, $unitOfWork, $this);
    }

    /**
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return HydratorFactoryInterface
     */
    protected function getHydratorFactory(UnitOfWorkInterface $unitOfWork)
    {
        if (! $this->hydratorFactory) {
            $proxyFactory = $this->buildProxyFactory($this->proxyConfiguration, $unitOfWork);
            $this->hydratorFactory = new HydratorFactory($this, $proxyFactory);
        }

        return $this->hydratorFactory;
    }
}
