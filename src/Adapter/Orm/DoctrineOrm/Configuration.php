<?php

namespace Graze\Dal\Adapter\Orm\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Graze\Dal\Adapter\Orm\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\Mapper\MapperInterface;
use Graze\Dal\Adapter\Orm\Persister\PersisterInterface;
use Graze\Dal\Adapter\Orm\UnitOfWork;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Mapper\EntityMapper;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Persister\EntityPersister;
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
     * @var EntityManager
     */
    private $em;

    /**
     * @param DalManager $dalManager
     * @param array $mapping
     * @param int $trackingPolicy
     * @param EntityManager $em
     */
    public function __construct(DalManager $dalManager, array $mapping, EntityManager $em, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        parent::__construct($dalManager, $mapping, $trackingPolicy);
        $this->proxyConfiguration = $this->buildProxyConfiguration();
        $this->em = $em;
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
        return new EntityPersister($entityName, $recordName, $unitOfWork, $this, $this->em);
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
