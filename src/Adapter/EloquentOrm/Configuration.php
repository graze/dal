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
namespace Graze\Dal\Adapter\EloquentOrm;

use Graze\Dal\Adapter\ActiveRecord\AbstractConfiguration;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\EloquentOrm\Mapper\EntityMapper;
use Graze\Dal\Adapter\EloquentOrm\Persister\EntityPersister;
use Graze\Dal\DalManager;

class Configuration extends AbstractConfiguration
{
    protected $hydratorFactory;
    protected $proxyConfiguration;

    /**
     * {@inheritdoc}
     */
    public function __construct(DalManager $dalManager, array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        $this->proxyConfiguration = $this->buildProxyConfiguration();

        parent::__construct($dalManager, $mapping, $trackingPolicy);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName($entity)
    {
        $inflector = $this->proxyConfiguration->getClassNameInflector();

        return $inflector->getUserClassName(get_class($entity));
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultMapper($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        return new EntityMapper($entityName, $recordName, $this->getHydratorFactory($unitOfWork));
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        return new EntityPersister($entityName, $recordName, $unitOfWork, $this);
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @return HydratorFactory
     */
    protected function getHydratorFactory(UnitOfWork $unitOfWork)
    {
        if (!$this->hydratorFactory) {
            $proxyFactory = $this->buildProxyFactory($this->proxyConfiguration, $unitOfWork);
            $this->hydratorFactory = new HydratorFactory($this, $proxyFactory);
        }

        return $this->hydratorFactory;
    }
}
