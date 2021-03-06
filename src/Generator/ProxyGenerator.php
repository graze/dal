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
namespace Graze\Dal\Generator;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Exception\MissingConfigException;
use ProxyManager\Configuration;

class ProxyGenerator implements GeneratorInterface
{
    /**
     * @var DalManagerInterface
     */
    private $dm;

    /**
     * @var string
     */
    protected $targetDir;

    /**
     * @param DalManagerInterface $dm
     * @param string $targetDir
     */
    public function __construct(DalManagerInterface $dm, $targetDir)
    {
        $this->dm = $dm;
        $this->targetDir = $targetDir;
    }

    /**
     * @return mixed
     * @throws MissingConfigException
     */
    public function generate()
    {
        $adapters = $this->dm->all();

        foreach ($adapters as $adapter) {
            $this->generateForAdapter($adapter);
        }
    }

    /**
     * @param \Graze\Dal\Adapter\AdapterInterface $adapter
     */
    protected function generateForAdapter(AdapterInterface $adapter)
    {
        $entityNames = $adapter->getConfiguration()->getEntityNames();
        $proxyConfig = new Configuration();
        $proxyConfig->setProxiesTargetDir($this->targetDir);
        $proxyConfig->setProxiesNamespace('Graze\\Dal');
        $proxyFactory = $adapter->getConfiguration()->buildProxyFactory($proxyConfig);

        foreach ($entityNames as $entityName) {
            $mapping = $adapter->getConfiguration()->getMapping($entityName);
            if (array_key_exists('related', $mapping)) {
                $related = $mapping['related'];

                foreach ($related as $relationName => $relationConfig) {
                    if (! array_key_exists('entity', $relationConfig)) {
                        throw new MissingConfigException($entityName, 'related.entity');
                    }

                    $foreignEntityName = $relationConfig['entity'];
                    if (array_key_exists('collection', $relationConfig) && $relationConfig['collection']) {
                        $collectionClass = is_string($relationConfig['collection']) ? $relationConfig['collection'] : null;
                        $proxyFactory->buildCollectionProxy($entityName, $foreignEntityName, function () {
                        }, $relationConfig, $collectionClass);
                    } else {
                        $proxyFactory->buildEntityProxy($entityName, $foreignEntityName, function () {
                        }, $relationConfig);
                    }
                }
            }
        }
    }
}
