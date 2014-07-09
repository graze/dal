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
namespace Graze\Dal;

use Closure;
use Exception;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Exception\UndefinedAdapterException;
use Graze\Dal\Exception\UndefinedRepositoryException;

class DalManager implements DalManagerInterface
{
    protected $adapters;

    /**
     * @param AdapterInterface[] $adapters
     */
    public function __construct(array $adapters = [])
    {
        foreach ($adapters as $name => $adapter) {
            $this->set($name, $adapter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new UndefinedAdapterException($name, __METHOD__);
        }

        return $this->adapters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return isset($this->adapters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, AdapterInterface $adapter)
    {
        $this->adapters[$name] = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($name)
    {
        try {
            $adapter = $this->findAdapterByEntityName($name);
        } catch (UndefinedAdapterException $e) {
            throw new UndefinedRepositoryException($name, __METHOD__, $e);
        }

        return $adapter->getRepository($name);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($entity = null)
    {
        $entityAdapter = $entity ? $this->findAdapterByEntity($entity) : null;

        foreach ($this->adapters as $adapter) {
            if ($adapter === $entityAdapter) {
                $adapter->flush($entity);
            } elseif (!$entityAdapter) {
                $adapter->flush();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persist($entity)
    {
        $adapter = $this->findAdapterByEntity($entity);
        $adapter->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($entity)
    {
        $adapter = $this->findAdapterByEntity($entity);
        $adapter->refresh($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function transaction($adapterName, callable $fn)
    {
        $adapter = $this->get($adapterName);
        $adapter->beginTransaction();

        if (!$fn instanceof Closure) {
            $fn = function ($adapter) use ($fn) { call_user_func($fn, $adapter); };
        }

        try {
            $fn($adapter);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }
    }

    /**
     * @param object $entity
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with entity
     */
    protected function findAdapterByEntity($entity)
    {
        foreach ($this->adapters as $adapter) {
            $name = $adapter->getEntityName($entity);

            if ($adapter->hasRepository($name)) {
                return $adapter;
            }
        }

        throw new UndefinedAdapterException(get_class($entity), __METHOD__);
    }

    /**
     * @param string $name
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with name
     */
    protected function findAdapterByEntityName($name)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->hasRepository($name)) {
                return $adapter;
            }
        }

        throw new UndefinedAdapterException($name, __METHOD__);
    }
}
