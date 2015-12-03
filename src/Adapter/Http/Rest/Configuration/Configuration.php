<?php

namespace Graze\Dal\Adapter\Http\Rest\Configuration;

use Graze\Dal\Adapter\Http\Rest\Persister\EntityPersister;
use Graze\Dal\Configuration\AbstractConfiguration;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\UnitOfWork\UnitOfWork;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use GuzzleHttp\ClientInterface;

class Configuration extends AbstractConfiguration
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param array $mapping
     * @param $trackingPolicy
     */
    public function __construct(ClientInterface $client, array $mapping, $trackingPolicy = UnitOfWork::POLICY_EXPLICIT)
    {
        parent::__construct($mapping, $trackingPolicy);
        $this->client = $client;
    }

    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     */
    protected function getRecordName($entityName, ConfigurationInterface $config)
    {
        $mapping = $config->getMapping($entityName);
        return $mapping['resource'];
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
        return new EntityPersister($entityName, $this->getRecordName($entityName, $config), $unitOfWork, $config, $this->client);
    }
}
