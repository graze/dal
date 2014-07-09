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
        $adapter = $this->findAdapterByEntityName($name);

        if (!$adapter) {
            throw new UndefinedRepositoryException($name, __METHOD__);
        }

        return $adapter->getRepository($name);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($object = null)
    {
        $objectAdapter = $object ? $this->findAdapterByEntity($object) : null;

        foreach ($this->adapters as $adapter) {
            if ($adapter === $objectAdapter) {
                $adapter->flush($object);
            } else {
                $adapter->flush();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object)
    {
        $adapter = $this->findAdapterByEntity($object);
        $adapter->persist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        $adapter = $this->findAdapterByEntity($object);
        $adapter->refresh($object);
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
     * @param object $object
     * @return AdapterInterface
     */
    protected function findAdapterByEntity($object)
    {
        return $this->findAdapterByEntityName(get_class($object));
    }

    /**
     * @param string $name
     * @return AdapterInterface
     */
    protected function findAdapterByEntityName($name)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->hasRepository($name)) {
                return $adapter;
            }
        }
    }
}
