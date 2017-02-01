<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Orm\DoctrineOrm;

use Doctrine\ORM\EntityManagerInterface;
use Graze\Dal\Adapter\Orm\Configuration\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Persister\EntityPersister;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class Configuration extends AbstractConfiguration
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(
        EntityManagerInterface $em,
        array $mapping,
        $trackingPolicy = UnitOfWork::POLICY_IMPLICIT
    ) {
        parent::__construct($mapping, $trackingPolicy);
        $this->em = $em;
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return PersisterInterface
     * @throws MissingConfigException
     */
    protected function buildDefaultPersister($entityName, ConfigurationInterface $config, UnitOfWorkInterface $unitOfWork)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            throw new MissingConfigException($entityName, 'record');
        }

        return new EntityPersister($entityName, $mapping['record'], $unitOfWork, $this, $this->em);
    }
}
