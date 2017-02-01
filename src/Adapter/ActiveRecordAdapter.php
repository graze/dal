<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter;

use Closure;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Graze\Dal\Adapter\Orm\OrmAdapterInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;

/**
 * @deprecated - DAL 0.x
 */
abstract class ActiveRecordAdapter implements OrmAdapterInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @var array
     */
    protected $repos = [];

    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @param ConfigurationInterface $config
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
        $this->unitOfWork = $config->buildUnitOfWork($this);
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity)
    {
        return $this->config->getEntityName($entity);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getRepository($name)
    {
        if (! $this->hasRepository($name)) {
            throw new UndefinedRepositoryException($name, __METHOD__);
        } elseif (! isset($this->repos[$name])) {
            $this->repos[$name] = $this->config->buildRepository($name, $this);
        }

        return $this->repos[$name];
    }

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRepository($name)
    {
        return (boolean) $this->config->getMapping($name);
    }

    /**
     * @param object|null $entity
     */
    public function flush($entity = null)
    {
        if (null !== $entity) {
            $this->unitOfWork->commit($entity);
        } else {
            $this->unitOfWork->commit();
        }
    }

    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->unitOfWork->persist($entity);
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $this->unitOfWork->refresh($entity);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->unitOfWork->remove($entity);
    }

    /**
     * @param callable $fn
     *
     * @throws \Exception
     */
    public function transaction(callable $fn)
    {
        if (! $fn instanceof Closure) {
            $fn = function ($adapter) use ($fn) {
                call_user_func($fn, $adapter);
            };
        }

        $this->beginTransaction();

        try {
            $fn($this);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
