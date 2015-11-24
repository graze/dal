<?php

namespace Graze\Dal\Adapter\Orm\Configuration;

use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactoryInterface;
use Graze\Dal\Adapter\Orm\Mapper\EntityMapper;
use Graze\Dal\Adapter\Orm\Proxy\ProxyFactory;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingGhostFactory;

abstract class AbstractConfiguration extends \Graze\Dal\Configuration\AbstractConfiguration
{
    /**
     * @var HydratorFactoryInterface
     */
    private $hydratorFactory;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return MapperInterface
     */
    protected function buildDefaultMapper($entityName, $recordName, UnitOfWorkInterface $unitOfWork)
    {
        return new EntityMapper($entityName, $recordName, $this->getHydratorFactory($unitOfWork), $this);
    }

    /**
     * @param ProxyConfiguration $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return ProxyFactory
     */
    protected function buildProxyFactory(ProxyConfiguration $config, UnitOfWorkInterface $unitOfWork)
    {
        return new ProxyFactory($this->dalManager, new LazyLoadingGhostFactory($config));
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
