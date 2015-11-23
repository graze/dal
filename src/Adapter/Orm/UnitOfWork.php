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
namespace Graze\Dal\Adapter\Orm;

use Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface;
use Graze\Dal\Adapter\ActiveRecord\Persister\PersisterInterface;
use SplObjectStorage;

class UnitOfWork
{
    const POLICY_IMPLICIT = 0;
    const POLICY_EXPLICIT = 1;

    protected $adapter;
    protected $config;
    protected $mappers = [];
    protected $persisted;
    protected $persisters = [];
    protected $records = [];
    protected $trackingPolicy;

    /**
     * @param OrmAdapter $adapter
     * @param ConfigurationInterface $config
     * @param integer $trackingPolicy
     */
    public function __construct(
        OrmAdapter $adapter,
        ConfigurationInterface $config,
        $trackingPolicy = self::POLICY_IMPLICIT
    ) {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->trackingPolicy = (integer) $trackingPolicy;

        $this->persisted = new SplObjectStorage();
    }

    /**
     * @param object $entity
     */
    public function commit($entity = null)
    {
        foreach ($this->persisted as $persisted) {
            if (!$entity || $entity === $persisted) {
                $this->getPersisterByEntity($persisted)->save($persisted);

                if ($entity) {
                    $this->persisted->detach($persisted);
                    return;
                }
            }
        }

        $this->persisted->removeAll($this->persisted);
    }

    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->persisted->attach($entity);
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $this->getPersisterByEntity($entity)->refresh($entity);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->getPersisterByEntity($entity)->delete($entity);
    }

    /**
     * @param object $entity
     */
    public function persistByTrackingPolicy($entity)
    {
        if (self::POLICY_IMPLICIT === $this->trackingPolicy) {
            $this->persist($entity);
        }
    }

    /**
     * @param object $entity
     */
    public function getEntityRecord($entity)
    {
        $hash = $this->config->getIdentityGenerator()->generate($entity);

        return isset($this->records[$hash]) ? $this->records[$hash] : null;
    }

    /**
     * @param object $entity
     * @param object $record
     */
    public function setEntityRecord($entity, $record)
    {
        $hash = $this->config->getIdentityGenerator()->generate($entity);

        $this->records[$hash] = $record;
    }

    /**
     * @param object $entity
     */
    public function removeEntityRecord($entity)
    {
        $hash = $this->config->getIdentityGenerator()->generate($entity);
        unset($this->records[$hash]);
    }

    /**
     * @return MapperInterface
     */
    public function getMapper($name)
    {
        if (!isset($this->mappers[$name])) {
            $this->mappers[$name] = $this->config->buildMapper($name, $this);
        }

        return $this->mappers[$name];
    }

    /**
     * @return PersisterInterface
     */
    public function getPersister($name)
    {
        if (!isset($this->persisters[$name])) {
            $this->persisters[$name] = $this->config->buildPersister($name, $this);
        }

        return $this->persisters[$name];
    }

    /**
     * @param object $entity
     * @return PersisterInterface
     */
    protected function getPersisterByEntity($entity)
    {
        return $this->getPersister($this->config->getEntityName($entity));
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}
