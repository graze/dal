<?php

namespace Graze\Dal\Adapter\DoctrineOrm;

use Graze\Dal\Adapter\ActiveRecord\AbstractConfiguration;
use Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface;
use Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Graze\Dal\Adapter\DoctrineOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\DoctrineOrm\Mapper\EntityMapper;
use Graze\Dal\Adapter\DoctrineOrm\Persister\EntityPersister;
use Graze\Dal\DalManager;

class Configuration extends AbstractConfiguration
{
    /**
     * @var \ProxyManager\Configuration
     */
    private $proxyConfiguration;

    /**
     * @var HydratorFactory
     */
    private $hydratorFactory;

    /**
     * @param DalManager $dalManager
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(DalManager $dalManager, array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        parent::__construct($dalManager, $mapping, $trackingPolicy);
        $this->proxyConfiguration = $this->buildProxyConfiguration();
    }

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     *
     * @return MapperInterface
     */
    protected function buildDefaultMapper($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        return new EntityMapper($entityName, $recordName, $this->getHydratorFactory($unitOfWork));
    }

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     *
     * @return PersisterInterface
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $unitOfWork)
    {
        return new EntityPersister($entityName, $recordName, $unitOfWork);
    }

    /**
     * @param UnitOfWork $unitOfWork
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
