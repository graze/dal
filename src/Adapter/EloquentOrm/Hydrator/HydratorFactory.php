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
namespace Graze\Dal\Adapter\EloquentOrm\Hydrator;

use CodeGenerationUtils\GeneratorStrategy\EvaluatingGeneratorStrategy;
use GeneratedHydrator\Configuration;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\Hydrator\AttributeHydrator;
use Graze\Dal\Adapter\ActiveRecord\Hydrator\MethodProxyHydrator;
use Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory;
use Graze\Dal\NamingStrategy\CamelCaseNamingStrategy;
use Graze\Dal\NamingStrategy\CombinedNamingStrategy;
use Graze\Dal\NamingStrategy\PrefixNamingStrategy;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;
use Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface;

class HydratorFactory
{
    protected $config;
    protected $proxyFactory;

    /**
     * @param ConfigurationInterface $config
     * @param ProxyFactory           $proxyFactory
     */
    public function __construct(ConfigurationInterface $config, ProxyFactory $proxyFactory)
    {
        $this->config = $config;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * @param string $entityName
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entityName)
    {
        $config = new Configuration($entityName);
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        return new $class();
    }

    /**
     * @param string $recordName
     * @return HydratorInterface
     */
    public function buildRecordHydrator($recordName)
    {
        $attributeHydrator = new AttributeHydrator('attributesToArray', 'fill');

        if ($attributeHydrator instanceof NamingStrategyEnabledInterface) {
            $strategy = $this->getNamingStrategyFromRecord($recordName);
            if (!is_null($strategy)) {
                $attributeHydrator->setNamingStrategy($strategy);
            }
        }

        return new MethodProxyHydrator(
            $this->config,
            $this->proxyFactory,
            $attributeHydrator
        );
    }

    /**
     * Get a naming strategy based on a supplied EloquentModel class name
     *
     * @param  string $recordName Model name
     * @return NamingStrategyInterface
     */
    public function getNamingStrategyFromRecord($recordName)
    {
        $strategy = new CombinedNamingStrategy();

        if (is_subclass_of($recordName, 'Graze\Dal\NamingStrategy\ColumnPrefixInterface')) {
            $obj = new $recordName();
            $strategy->addNamingStrategy(new PrefixNamingStrategy($obj->getColumnPrefix()), 1);
        }

        $strategy->addNamingStrategy(new CamelCaseNamingStrategy());

        return $strategy;
    }
}
