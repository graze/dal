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
use Graze\Dal\DalManagerAwareInterface;
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
use Graze\Dal\Proxy\ProxyFactory;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Graze\Dal\Relationship\ManyToManyResolver;
use Graze\Dal\Relationship\ManyToOneResolver;
use Graze\Dal\Relationship\OneToManyResolver;
use Graze\Dal\Relationship\RelationshipResolver;
use Graze\Dal\Repository\EntityRepository;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingGhostFactory;

abstract class AbstractConfiguration implements ConfigurationInterface, DalManagerAwareInterface
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
     * @var DalManagerInterface
     */
    private $dm;

    /**
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        $this->mapping = $mapping;
        $this->trackingPolicy = $trackingPolicy;

        $this->identityGenerator = $this->buildDefaultIdentityGenerator();
        $this->proxyConfiguration = $this->buildProxyConfiguration();
    }

    /**
     * @param DalManagerInterface $dm
     */
    public function setDalManager(DalManagerInterface $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return MapperInterface
     */
    protected function buildDefaultMapper($entityName, ConfigurationInterface $config)
    {
        return new EntityMapper($entityName, $this->getRecordName($entityName, $config), $this->getHydratorFactory(), $config);
    }

    /**
     * @return HydratorFactoryInterface
     */
    protected function getHydratorFactory()
    {
        if (! $this->hydratorFactory) {
            $proxyFactory = $this->buildProxyFactory($this->proxyConfiguration);
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
    public function buildMapper($name)
    {
        return $this->buildDefaultMapper($name, $this);
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
     *
     * @return ProxyFactoryInterface
     */
    protected function buildProxyFactory(ProxyConfiguration $config)
    {
        $resolver = new RelationshipResolver(
            new ManyToManyResolver($this->dm),
            new ManyToOneResolver($this->dm),
            new OneToManyResolver($this->dm)
        );

        return new ProxyFactory($this->dm, $resolver, new LazyLoadingGhostFactory($config));
    }
}
