<?php

namespace Graze\Dal\Adapter\Orm\Configuration;

use Graze\Dal\Adapter\Orm\Relationship\ManyToManyResolver;
use Graze\Dal\Adapter\Orm\Relationship\OrmResolver;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Proxy\ProxyFactory;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Graze\Dal\Relationship\ManyToOneResolver;
use Graze\Dal\Relationship\OneToManyResolver;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingGhostFactory;

abstract class AbstractConfiguration extends \Graze\Dal\Configuration\AbstractConfiguration
{
    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     */
    protected function getRecordName($entityName, ConfigurationInterface $config)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $entityName);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $mapping['record'];
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
}
