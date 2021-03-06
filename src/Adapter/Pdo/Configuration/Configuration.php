<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Pdo\Configuration;

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Adapter\Pdo\Persister\EntityPersister;
use Graze\Dal\Configuration\AbstractConfiguration;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class Configuration extends AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @var ExtendedPdo
     */
    private $db;

    /**
     * @param ExtendedPdo $db
     * @param array $mapping
     * @param int $trackingPolicy
     */
    public function __construct(ExtendedPdo $db, array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        parent::__construct($mapping, $trackingPolicy);
        $this->db = $db;
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     * @param UnitOfWorkInterface $unitOfWork
     *
     * @return PersisterInterface
     */
    protected function buildDefaultPersister(
        $entityName,
        ConfigurationInterface $config,
        UnitOfWorkInterface $unitOfWork
    ) {
        return new EntityPersister($entityName, $this->getRecordName($entityName, $config), $unitOfWork, $config, $this->db);
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     * @throws MissingConfigException
     */
    protected function getRecordName($entityName, ConfigurationInterface $config)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('table', $mapping)) {
            throw new MissingConfigException($entityName, 'table');
        }

        return $mapping['table'];
    }
}
