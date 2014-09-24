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

use GeneratedHydrator\Configuration;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\Hydrator\AttributeHydrator;
use Graze\Dal\Adapter\ActiveRecord\Hydrator\MethodProxyHydrator;
use Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory;
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
     * @param string $entityName
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entityName)
    {
        $config = new Configuration($entityName);
        $class  = $config->createFactory()->getHydratorClass();

        return new $class();
    }

    /**
     * @param string $recordName
     * @return HydratorInterface
     */
    public function buildRecordHydrator($recordName)
    {
        return new MethodProxyHydrator(
            $this->config,
            $this->proxyFactory,
            new AttributeHydrator('attributesToArray', 'fill')
        );
    }
}