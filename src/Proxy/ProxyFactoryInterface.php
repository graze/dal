<?php

namespace Graze\Dal\Proxy;

use ProxyManager\Proxy\GhostObjectInterface;

interface ProxyFactoryInterface
{
    /**
     * @param string $localClass
     * @param string $foreignClass
     * @param callable $id
     * @param array $config
     * @param string $collectionClass
     *
     * @return GhostObjectInterface
     */
    public function buildCollectionProxy($localClass, $foreignClass, callable $id, array $config, $collectionClass = null);

    /**
     * @param string $localClass
     * @param string $foreignClass
     * @param callable $id
     * @param array $config
     *
     * @return GhostObjectInterface
     */
    public function buildEntityProxy($localClass, $foreignClass, callable $id, array $config);
}
