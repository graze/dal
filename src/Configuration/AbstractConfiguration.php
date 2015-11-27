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
namespace Graze\Dal\Configuration;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Entity\EntityMetadata;
use Graze\Dal\Exception\InvalidRepositoryException;
use Graze\Dal\Hydrator\HydratorFactory;
use Graze\Dal\Hydrator\HydratorFactoryInterface;
use Graze\Dal\Identity\GeneratorInterface;
use Graze\Dal\Identity\ObjectHashGenerator;
use Graze\Dal\Mapper\EntityMapper;
use Graze\Dal\Mapper\MapperInterface;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Graze\Dal\Repository\EntityRepository;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use ProxyManager\Configuration as ProxyConfiguration;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    const PROXY_NAMESPACE = 'Graze\Dal';

    /**
     * @var GeneratorInterface
     */
    protected $identityGenerator;

    /**
     * @var array
     */
    protected $mapping;

    /**
     * @var int
     */
    protected $trackingPolicy;

    /**
     * @var ProxyConfiguration
     */
    protected $proxyConfiguration;

    /**
     * @var HydratorFactoryInterface
     */
    private $hydratorFactory;

    /**
     * @param DalManagerInterface $dalManager
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(DalManagerInterface $dalManager, array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        $this->mapping = $mapping;
        $this->trackingPolicy = $trackingPolicy;
        $this->dalManager = $dalManager;

        $this->identityGenerator = $this->buildDefaultIdentityGenerator();
        $this->proxyConfiguration = $this->buildProxyConfiguration();
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return MapperInterface
     */
    protected function buildDefaultMapper($entityName, ConfigurationInterface $config, UnitOfWorkInterface $unitOfWork)
    {
        return new EntityMapper($entityName, $this->getRecordName($entityName, $config), $this->getHydratorFactory($unitOfWork), $config);
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

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     */
    abstract protected function getRecordName($entityName, ConfigurationInterface $config);

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return PersisterInterface
     */
    abstract protected function buildDefaultPersister($entityName, ConfigurationInterface $config, UnitOfWorkInterface $unitOfWork);

    /**
     * {@inheritdoc}
     */
    public function buildMapper($name, UnitOfWorkInterface $unitOfWork)
    {
        return $this->buildDefaultMapper($name, $this, $unitOfWork);
    }

    /**
     * {@inheritdoc}
     */
    public function buildPersister($name, UnitOfWorkInterface $unitOfWork)
    {
        return $this->buildDefaultPersister($name, $this, $unitOfWork);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRepository($name, AdapterInterface $adapter)
    {
        $mapping = $this->getMapping($name);

        if (isset($mapping['repository'])) {
            $class = $mapping['repository'];
            $repo = new $class($name, $adapter);

            if (! $repo instanceof ObjectRepository) {
                throw new InvalidRepositoryException($repo, __METHOD__);
            }
        } else {
            $repo = $this->buildDefaultRepository($name, $adapter);
        }

        return $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUnitOfWork(AdapterInterface $adapter)
    {
        return new UnitOfWork($adapter, $this, $this->trackingPolicy);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName($entity)
    {
        return get_class($entity);
    }

    /**
     * @return GeneratorInterface
     */
    public function getIdentityGenerator()
    {
        return $this->identityGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping($name)
    {
        return isset($this->mapping[$name]) ? $this->mapping[$name] : null;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return EntityMetadata
     */
    public function buildEntityMetadata(EntityInterface $entity)
    {
        return new EntityMetadata($entity, $this);
    }

    /**
     * @return GeneratorInterface
     */
    protected function buildDefaultIdentityGenerator()
    {
        return new ObjectHashGenerator();
    }

    /**
     * @param string $name
     * @param AdapterInterface $adapter
     *
     * @return ObjectRepository
     */
    protected function buildDefaultRepository($name, AdapterInterface $adapter)
    {
        return new EntityRepository($name, $adapter);
    }

    /**
     * @param string $namespace
     *
     * @return ProxyConfiguration
     */
    protected function buildProxyConfiguration($namespace = self::PROXY_NAMESPACE)
    {
        $config = new ProxyConfiguration();
        $config->setProxiesNamespace($namespace);

        return $config;
    }

    /**
     * @param ProxyConfiguration $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return ProxyFactoryInterface
     */
    abstract protected function buildProxyFactory(ProxyConfiguration $config, UnitOfWorkInterface $unitOfWork);
}
