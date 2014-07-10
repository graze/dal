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
namespace Graze\Dal\Adapter\EloquentOrm;

use Graze\Dal\Adapter\ActiveRecord\AbstractConfiguration;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;

class Configuration extends AbstractConfiguration
{
    protected $hydratorFactory;

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultMapper($entityName, $recordName)
    {
        return new EntityMapper($entityName, $recordName, $this->getHydratorFactory());
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $uow)
    {
        return new EntityPersister($entityName, $recordName, $uow);
    }

    /**
     * @return HydratorFactory
     */
    protected function getHydratorFactory()
    {
        if (!$this->hydratorFactory) {
            $this->hydratorFactory = new HydratorFactory();
        }

        return $this->hydratorFactory;
    }
}
