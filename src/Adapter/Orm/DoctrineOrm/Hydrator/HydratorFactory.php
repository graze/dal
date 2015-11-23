<?php

namespace Graze\Dal\Adapter\Orm\DoctrineOrm\Hydrator;

use CodeGenerationUtils\GeneratorStrategy\EvaluatingGeneratorStrategy;
use GeneratedHydrator\Configuration;
use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Hydrator\FieldMappingHydrator;
use Graze\Dal\Adapter\Orm\Hydrator\MethodProxyHydrator;
use Graze\Dal\Adapter\Orm\Proxy\ProxyFactory;

class HydratorFactory
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    /**
     * @param ConfigurationInterface $config
     * @param ProxyFactory $proxyFactory
     */
    public function __construct(ConfigurationInterface $config, ProxyFactory $proxyFactory)
    {
        $this->config = $config;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * @param object $entity
     *
     * @return MethodProxyHydrator
     */
    public function buildEntityHydrator($entity)
    {
        $config = new Configuration($this->config->getEntityName($entity));
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        $hydrator = new $class();

        return new MethodProxyHydrator($this->config, $this->proxyFactory, new FieldMappingHydrator($this->config, $hydrator));
    }

    /**
     * @param object $record
     *
     * @return mixed
     */
    public function buildRecordHydrator($record)
    {
        $config = new Configuration(get_class($record));
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        $hydrator = new $class();

        return new FieldMappingHydrator($this->config, $hydrator);
    }
}
