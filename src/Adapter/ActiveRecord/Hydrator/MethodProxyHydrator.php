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
namespace Graze\Dal\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory;
use LogicException;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * @deprecated - DAL 0.x
 */
class MethodProxyHydrator implements HydratorInterface
{
    protected $config;
    protected $next;
    protected $proxyFactory;

    /**
     * @param ConfigurationInterface $config
     * @param ProxyFactory $proxyFactory
     * @param HydratorInterface $next
     */
    public function __construct(
        ConfigurationInterface $config,
        ProxyFactory $proxyFactory,
        HydratorInterface $next = null
    ) {
        $this->config = $config;
        $this->proxyFactory = $proxyFactory;
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        $entityName = $this->config->getEntityNameFromRecord($object);
        $mapping = $this->formatMapping($this->config->getMapping($entityName) ?: []);

        $out = array_map(function ($map) use ($object) {
            $args = $map['args'];
            $entity = $map['entity'];
            $callable = [$object, $map['method']];

            if ($map['collection']) {
                $collectionClass = is_string($map['collection']) ? $map['collection'] : null;
                return $this->proxyFactory->buildCollectionProxy($entity, $callable, $collectionClass, $args);
            } else {
                return $this->proxyFactory->buildEntityProxy($entity, $callable, $args);
            }
        }, $mapping);

        if ($this->next) {
            $out += $this->next->extract($object);
        }

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        if ($this->next) {
            $this->next->hydrate($data, $object);
        }

        return $object;
    }

    /**
     * @param array $mapping
     *
     * @return array
     */
    protected function formatMapping(array $mapping)
    {
        if (! isset($mapping['related']) || ! is_array($mapping['related'])) {
            return [];
        }

        return array_map(function ($map) {
            $out = [
                'args' => isset($map['args']) ? $map['args'] : [],
                'entity' => isset($map['entity']) ? $map['entity'] : null,
                'method' => isset($map['method']) ? $map['method'] : null,
                'collection' => isset($map['collection']) ? $map['collection'] : false,
            ];

            if (! $out['entity'] || ! $out['method']) {
                $message = 'Relationship mapping must contain "entity" and "method" values';
                throw new InvalidMappingException($message, __METHOD__);
            }

            return $out;
        }, $mapping['related']);
    }
}
