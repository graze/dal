<?php

namespace Graze\Dal\Generator;

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
    private $targetDir;

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
                            $proxyFactory->buildCollectionProxy($entityName, $foreignEntityName, function () {}, $relationConfig, $collectionClass);
                        } else {
                            $proxyFactory->buildEntityProxy($entityName, $foreignEntityName, function () {
                            }, $relationConfig);
                        }
                    }
                }
            }
        }
    }
}
