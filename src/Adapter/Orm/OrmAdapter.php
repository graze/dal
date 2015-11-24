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

use Closure;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

abstract class OrmAdapter implements OrmAdapterInterface
{
    protected $config;
    protected $repos = [];
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
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity)
    {
        return $this->config->getEntityName($entity);
    }

    /**
     * {@inheritdoc}
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
     * @return UnitOfWorkInterface
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRepository($name)
    {
        return (boolean) $this->config->getMapping($name);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function persist($entity)
    {
        $this->unitOfWork->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($entity)
    {
        $this->unitOfWork->refresh($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity)
    {
        $this->unitOfWork->remove($entity);
    }

    /**
     * {@inheritdoc}
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
