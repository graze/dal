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
use Graze\Dal\Adapter\EloquentOrm\EntityMapper;
use Graze\Dal\Adapter\EloquentOrm\EntityPersister;
use Graze\Dal\Adapter\EloquentOrm\Hydrator\HydratorFactory;
use Graze\Dal\Exception\InvalidMappingException;

class Configuration extends AbstractConfiguration
{
    protected $hydratorFactory;

    /**
     * {@inheritdoc}
     */
    public function buildMapper($name)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return new EntityMapper($name, $mapping['record'], $this->getHydratorFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function buildPersister($name, MapperInterface $mapper, UnitOfWork $uow)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return new EntityPersister($name, $mapping['record'], $mapper, $uow);
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
