<?php

namespace Graze\Dal\Proxy;

use ProxyManager\Proxy\GhostObjectInterface;

interface ProxyFactoryInterface
{
    /**
     * @param string $class
     * @param callable $id
     * @param array $config
     * @param string $collectionClass
     *
     * @return GhostObjectInterface
     */
    public function buildCollectionProxy($class, callable $id, array $config, $collectionClass = null);

    /**
     * @param string $class
     * @param callable $id
     * @param array $config
     *
     * @return GhostObjectInterface
     */
    public function buildEntityProxy($class, callable $id, array $config);
}
