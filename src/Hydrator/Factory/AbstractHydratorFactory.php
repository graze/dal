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
namespace Graze\Dal\Hydrator\Factory;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Hydrator\EntityFieldMappingHydrator;
use Graze\Dal\Hydrator\RecordFieldMappingHydrator;
use Graze\Dal\Hydrator\RelationshipProxyHydrator;
use Graze\Dal\Hydrator\RuntimeCacheHydrator;
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

        return new RuntimeCacheHydrator(new RelationshipProxyHydrator(
            $this->config,
            $this->proxyFactory,
            new EntityFieldMappingHydrator($this->config, $defaultHydrator)
        ));
    }

    /**
     * @param object $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record)
    {
        $defaultHydrator = $this->buildDefaultRecordHydrator($record);
        return new RuntimeCacheHydrator(new RecordFieldMappingHydrator($this->config, $defaultHydrator));
    }
}
