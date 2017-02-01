<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
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
