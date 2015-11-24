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
use Graze\Dal\Adapter\Orm\Configuration\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Persister\EntityPersister;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class Configuration extends AbstractConfiguration
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param DalManagerInterface $dalManager
     * @param array $mapping
     * @param EntityManager $em
     * @param int $trackingPolicy
     */
    public function __construct(
        DalManagerInterface $dalManager,
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
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return PersisterInterface
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWorkInterface $unitOfWork)
    {
        return new EntityPersister($entityName, $recordName, $unitOfWork, $this, $this->em);
    }
}
