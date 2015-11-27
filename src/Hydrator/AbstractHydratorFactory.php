<?php

namespace Graze\Dal\Hydrator;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

abstract class AbstractHydratorFactory implements HydratorFactoryInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @var ProxyFactoryInterface
     */
    private $proxyFactory;

    /**
     * @param ConfigurationInterface $config
     * @param ProxyFactoryInterface $proxyFactory
     */
    public function __construct(ConfigurationInterface $config, ProxyFactoryInterface $proxyFactory)
    {
        $this->config = $config;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    abstract protected function buildDefaultEntityHydrator($entity);

    /**
     * @param object $record
     *
     * @return HydratorInterface
     */
    abstract protected function buildDefaultRecordHydrator($record);

    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entity)
    {
        $defaultHydrator = $this->buildDefaultEntityHydrator($entity);

        return new RelationshipProxyHydrator(
            $this->config,
            $this->proxyFactory,
            new FieldMappingHydrator($this->config, $defaultHydrator)
        );
    }

    /**
     * @param object $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record)
    {
        $defaultHydrator = $this->buildDefaultRecordHydrator($record);
        return new FieldMappingHydrator($this->config, $defaultHydrator);
    }
}
