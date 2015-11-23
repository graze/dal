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

use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Hydrator\AttributeHydrator;
use Graze\Dal\Adapter\Orm\Hydrator\FieldMappingHydrator;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactory as OrmHydratorFactory;
use Graze\Dal\Adapter\Orm\Hydrator\HydratorFactoryInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratorFactory implements HydratorFactoryInterface
{
    /**
     * @var OrmHydratorFactory
     */
    private $hydratorFactory;

    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @param OrmHydratorFactory $hydratorFactory
     * @param ConfigurationInterface $config
     */
    public function __construct(OrmHydratorFactory $hydratorFactory, ConfigurationInterface $config)
    {
        $this->hydratorFactory = $hydratorFactory;
        $this->config = $config;
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

    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entity)
    {
        return $this->hydratorFactory->buildEntityHydrator($entity);
    }
}
