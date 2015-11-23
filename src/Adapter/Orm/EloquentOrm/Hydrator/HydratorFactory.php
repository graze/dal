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
namespace Graze\Dal\Adapter\Orm\EloquentOrm\Hydrator;

use CodeGenerationUtils\GeneratorStrategy\EvaluatingGeneratorStrategy;
use GeneratedHydrator\Configuration;
use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Hydrator\AttributeHydrator;
use Graze\Dal\Adapter\Orm\Hydrator\FieldMappingHydrator;
use Graze\Dal\Adapter\Orm\Hydrator\MethodProxyHydrator;
use Graze\Dal\Adapter\Orm\Proxy\ProxyFactory;
use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratorFactory
{
    protected $config;
    protected $proxyFactory;

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
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entity)
    {
        $config = new Configuration($this->config->getEntityName($entity));
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        $hydrator = new $class();

        return new MethodProxyHydrator(
            $this->config,
            $this->proxyFactory,
            new FieldMappingHydrator($this->config, $hydrator)
        );
    }

    /**
     * @param object $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record)
    {
        $attributeHydrator = new AttributeHydrator('attributesToArray', 'fill');
        return new FieldMappingHydrator($this->config, $attributeHydrator);
    }
}
