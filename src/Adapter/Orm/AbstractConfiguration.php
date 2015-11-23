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
namespace Graze\Dal\Adapter\Orm;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\Orm\Mapper\EntityMapper;
use Graze\Dal\Adapter\Orm\Mapper\MapperInterface;
use Graze\Dal\Adapter\Orm\Persister\PersisterInterface;
use Graze\Dal\Adapter\Orm\Identity\GeneratorInterface;
use Graze\Dal\Adapter\Orm\Identity\ObjectHashGenerator;
use Graze\Dal\Adapter\Orm\Proxy\ProxyFactory;
use Graze\Dal\DalManager;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Entity\EntityMetadata;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Exception\InvalidRepositoryException;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingGhostFactory;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    const PROXY_NAMESPACE = 'Graze\Dal';

    protected $identityGenerator;
    protected $mapping;
    protected $trackingPolicy;
	protected $proxyConfiguration;

    /**
     * @param DalManager $dalManager
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(DalManager $dalManager, array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        $this->mapping = $mapping;
        $this->trackingPolicy = $trackingPolicy;
        $this->dalManager = $dalManager;

        $this->identityGenerator = $this->buildDefaultIdentityGenerator();
	    $this->proxyConfiguration = $this->buildProxyConfiguration();
    }

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     * @return MapperInterface
     */
	protected function buildDefaultMapper($entityName, $recordName, UnitOfWork $unitOfWork)
	{
		return new EntityMapper($entityName, $recordName, $this->getHydratorFactory($unitOfWork), $this);
	}

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     * @return PersisterInterface
     */
    abstract protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $unitOfWork);

    /**
     * {@inheritdoc}
     */
    public function buildMapper($name, UnitOfWork $unitOfWork)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $this->buildDefaultMapper($name, $mapping['record'], $unitOfWork);
    }

    /**
     * {@inheritdoc}
     */
    public function buildPersister($name, UnitOfWork $unitOfWork)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $this->buildDefaultPersister($name, $mapping['record'], $unitOfWork);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRepository($name, OrmAdapter $adapter)
    {
        $mapping = $this->getMapping($name);

        if (isset($mapping['repository'])) {
            $class = $mapping['repository'];
            $repo = new $class($name, $adapter);

            if (!$repo instanceof ObjectRepository) {
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
    public function buildUnitOfWork(OrmAdapter $adapter)
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
     * @param object $record
     * @return string
     */
    public function getEntityNameFromRecord($record)
    {
        $class = get_class($record);

        foreach ($this->mapping as $name => $mapping) {
            if (isset($mapping['record']) && $class === $mapping['record']) {
                return $name;
            }
        }
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
     * @param OrmAdapter $adapter
     * @return EntityRepository
     */
    protected function buildDefaultRepository($name, OrmAdapter $adapter)
    {
        return new EntityRepository($name, $adapter);
    }

    /**
     * @param string $namespace
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
     * @param UnitOfWork $unitOfWork
     * @return ProxyFactory
     */
    protected function buildProxyFactory(ProxyConfiguration $config, UnitOfWork $unitOfWork)
    {
        return new ProxyFactory($this->dalManager, new LazyLoadingGhostFactory($config));
    }
}
