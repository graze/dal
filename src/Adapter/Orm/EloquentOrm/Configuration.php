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
namespace Graze\Dal\Adapter\Orm\EloquentOrm;

use Graze\Dal\Adapter\Orm\Configuration\AbstractConfiguration;
use Graze\Dal\Adapter\Orm\EloquentOrm\Hydrator\HydratorFactory;
use Graze\Dal\Adapter\Orm\EloquentOrm\Persister\EntityPersister;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Hydrator\HydratorFactoryInterface;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class Configuration extends AbstractConfiguration
{
    /**
     * @var HydratorFactoryInterface
     */
    private $hydratorFactory;

    /**
     * {@inheritdoc}
     * @throws InvalidMappingException
     */
    protected function buildDefaultPersister($entityName, ConfigurationInterface $config, UnitOfWorkInterface $unitOfWork)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $entityName);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return new EntityPersister($entityName, $mapping['record'], $unitOfWork, $this);
    }

    /**
     * @return HydratorFactoryInterface
     */
    protected function getHydratorFactory()
    {
        if (! $this->hydratorFactory) {
            $proxyFactory = $this->buildProxyFactory($this->proxyConfiguration);
            $this->hydratorFactory = new HydratorFactory($this, $proxyFactory);
        }

        return $this->hydratorFactory;
    }
}
