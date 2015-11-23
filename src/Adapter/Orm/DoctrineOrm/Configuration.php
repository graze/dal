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
namespace Graze\Dal\Adapter\Orm\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Graze\Dal\Adapter\Orm\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactoryInterface;
use Graze\Dal\Adapter\Orm\Persister\PersisterInterface;
use Graze\Dal\Adapter\Orm\UnitOfWork;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Persister\EntityPersister;
use Graze\Dal\DalManager;

class Configuration extends AbstractConfiguration
{
    /**
     * @var HydratorFactoryInterface
     */
    private $hydratorFactory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param DalManager $dalManager
     * @param array $mapping
     * @param EntityManager $em
     * @param int $trackingPolicy
     */
    public function __construct(
        DalManager $dalManager,
        array $mapping,
        EntityManager $em,
        $trackingPolicy = UnitOfWork::POLICY_IMPLICIT
    ) {
        parent::__construct($dalManager, $mapping, $trackingPolicy);
        $this->em = $em;
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
     *
     * @return HydratorFactoryInterface
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
