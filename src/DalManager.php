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
namespace Graze\Dal;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Exception\UndefinedAdapterException;
use Graze\Dal\Exception\UndefinedRepositoryException;

class DalManager implements DalManagerInterface
{
    /**
     * @var AdapterInterface[]
     */
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
     * @param string $name
     *
     * @return AdapterInterface
     */
    public function get($name)
    {
        if (! $this->has($name)) {
            throw new UndefinedAdapterException($name, __METHOD__);
        }

        return $this->adapters[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->adapters[$name]);
    }

    /**
     * @param string $name
     * @param AdapterInterface $adapter
     */
    public function set($name, AdapterInterface $adapter)
    {
        if ($adapter instanceof DalManagerAwareInterface) {
            $adapter->setDalManager($this);
        }
        $this->adapters[$name] = $adapter;
    }

    /**
     * @param string $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
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
     * @param object|null $entity
     */
    public function flush($entity = null)
    {
        $entityAdapter = $entity ? $this->findAdapterByEntity($entity) : null;

        foreach ($this->adapters as $adapter) {
            if ($adapter === $entityAdapter) {
                $adapter->flush($entity);
            } elseif (! $entityAdapter) {
                $adapter->flush();
            }
        }
    }

    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $adapter = $this->findAdapterByEntity($entity);
        $adapter->persist($entity);
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $adapter = $this->findAdapterByEntity($entity);
        $adapter->refresh($entity);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $adapter = $this->findAdapterByEntity($entity);
        $adapter->remove($entity);
    }

    /**
     * @param string $adapterName
     * @param callable $fn
     */
    public function transaction($adapterName, callable $fn)
    {
        $adapter = $this->get($adapterName);
        $adapter->transaction($fn);
    }

    /**
     * @param object $entity
     *
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with entity
     */
    public function findAdapterByEntity($entity)
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
     *
     * @return AdapterInterface
     * @throws UndefinedAdapterException If the adapter is not registered with name
     */
    public function findAdapterByEntityName($name)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->hasRepository($name)) {
                return $adapter;
            }
        }

        throw new UndefinedAdapterException($name, __METHOD__);
    }

    /**
     * @return AdapterInterface[]
     */
    public function all()
    {
        return $this->adapters;
    }
}
