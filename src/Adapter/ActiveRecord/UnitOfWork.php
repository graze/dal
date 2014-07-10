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
namespace Graze\Dal\Adapter\ActiveRecord;

use Graze\Dal\Adapter\ActiveRecordAdapter;
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
    protected $trackingPolicy;

    /**
     * @param ActiveRecordAdapter $adapter
     * @param ConfigurationInterface $config
     * @param integer $trackingPolicy
     */
    public function __construct(
        ActiveRecordAdapter $adapter,
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
                $this->persisted->detatch($persisted);

                if ($entity) {
                    break;
                }
            }
        }
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
        throw new \LogicException('Refresh isn\'t implemented yet!');
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
     * @return MapperInterface
     */
    public function getMapper($name)
    {
        if (!isset($this->mappers[$name])) {
            $this->mappers[$name] = $this->config->buildMapper($name);
        }

        return $this->mappers[$name];
    }

    /**
     * @return PersisterInterface
     */
    public function getPersister($name)
    {
        if (!isset($this->persisters[$name])) {
            $mapper = $this->getMapper($name);
            $this->persisters[$name] = $this->config->buildPersister($name, $mapper, $this);
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
}
