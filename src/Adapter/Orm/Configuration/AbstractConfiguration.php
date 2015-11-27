<?php

namespace Graze\Dal\Adapter\Orm\Configuration;

use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactoryInterface;
use Graze\Dal\Adapter\Orm\Mapper\EntityMapper;
use Graze\Dal\Adapter\Orm\Relationship\ManyToManyResolver;
use Graze\Dal\Adapter\Orm\Relationship\ManyToOneResolver;
use Graze\Dal\Adapter\Orm\Relationship\OneToManyResolver;
use Graze\Dal\Adapter\Orm\Relationship\OrmResolver;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Proxy\ProxyFactory;
use Graze\Dal\Proxy\ProxyFactoryInterface;
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
     * @param ConfigurationInterface $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return MapperInterface
     * @throws InvalidMappingException
     */
    protected function buildDefaultMapper($entityName, ConfigurationInterface $config, UnitOfWorkInterface $unitOfWork)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $entityName);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return new EntityMapper($entityName, $mapping['record'], $this->getHydratorFactory($unitOfWork), $this);
    }

    /**
     * @param ProxyConfiguration $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return ProxyFactoryInterface
     */
    protected function buildProxyFactory(ProxyConfiguration $config, UnitOfWorkInterface $unitOfWork)
    {
        $resolver = new OrmResolver(
            new ManyToManyResolver($this->dalManager),
            new ManyToOneResolver($this->dalManager),
            new OneToManyResolver($this->dalManager)
        );

        return new ProxyFactory($this->dalManager, $resolver, new LazyLoadingGhostFactory($config));
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
