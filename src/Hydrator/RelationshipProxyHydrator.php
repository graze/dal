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
namespace Graze\Dal\Hydrator;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Exception\MissingConfigException;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class RelationshipProxyHydrator implements HydratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @var HydratorInterface
     */
    protected $next;

    /**
     * @var ProxyFactoryInterface
     */
    protected $proxyFactory;

    /**
     * @param ConfigurationInterface $config
     * @param ProxyFactoryInterface $proxyFactory
     * @param HydratorInterface $next
     */
    public function __construct(
        ConfigurationInterface $config,
        ProxyFactoryInterface $proxyFactory,
        HydratorInterface $next = null
    ) {
        $this->config = $config;
        $this->proxyFactory = $proxyFactory;
        $this->next = $next;
    }

    /**
     * @param object $object
     *
     * @return array
     * @throws MissingConfigException
     */
    public function extract($object)
    {
        $out = [];
        if ($this->next) {
            $out += $this->next->extract($object);
        }

        $entityName = $this->config->getEntityName($object);
        $mapping = $this->formatMapping($this->config->getMapping($entityName));

        foreach ($out as $field => $value) {
            if (is_object($value) && $value instanceof EntityInterface) {
                if (! array_key_exists($field, $mapping)) {
                    throw new MissingConfigException($entityName, 'related.' . $field);
                }

                $map = $mapping[$field];

                if (! array_key_exists('type', $map)) {
                    throw new MissingConfigException($entityName, 'related.' . $field . '.type');
                }

                if ($map['type'] !== 'manyToMany') {
                    if (! array_key_exists('localKey', $map)) {
                        throw new MissingConfigException($entityName, 'related.' . $field . '.localKey');
                    }

                    unset($out[$field]);
                    $out[$map['localKey']] = $value->getId();
                }
            }
        }

        return $out;
    }

    /**
     * @param array $data
     * @param object $object
     *
     * @return object
     * @throws MissingConfigException
     */
    public function hydrate(array $data, $object)
    {
        $entityName = $this->config->getEntityName($object);
        $mapping = $this->formatMapping($this->config->getMapping($entityName) ?: []);

        $out = array_map(function ($map) use ($data, $object, $entityName) {
            if (! array_key_exists('entity', $map)) {
                throw new MissingConfigException($entityName, 'related.entity');
            }

            if (! array_key_exists('type', $map)) {
                throw new MissingConfigException($entityName, 'related.type');
            }

            $foreignEntity = $map['entity'];

            if ($map['collection']) {
                $collectionClass = is_string($map['collection']) ? $map['collection'] : null;
                $id = function () use ($map, $data, $object) {
                    return $map['type'] === 'manyToMany' ? (int) $data['id'] : $object->getId();
                };
                return $this->proxyFactory->buildCollectionProxy($entityName, $foreignEntity, $id, $map, $collectionClass);
            } else {
                if (! array_key_exists('localKey', $map)) {
                    throw new MissingConfigException($entityName, 'related.localKey');
                }

                $id = function () use ($data, $map) {
                    return $map['type'] === 'manyToMany' ? (int) $data['id'] : (int) $data[$map['localKey']];
                };
                return $this->proxyFactory->buildEntityProxy($entityName, $foreignEntity, $id, $map);
            }
        }, $mapping);

        $data += $out;

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
                'entity' => isset($map['entity']) ? $map['entity'] : null,
                'type' => isset($map['type']) ? $map['type'] : null,
                'collection' => isset($map['collection']) ? $map['collection'] : false,
                'localKey' => isset($map['localKey']) ? $map['localKey'] : null,
                'foreignKey' => isset($map['foreignKey']) ? $map['foreignKey'] : null,
                'pivot' => isset($map['pivot']) ? $map['pivot'] : null,
            ];

            if (! $out['entity'] || ! $out['type']) {
                $message = 'Relationship mapping must contain "entity" and "type" values';
                throw new InvalidMappingException($message, __METHOD__);
            }

            return $out;
        }, $mapping['related']);
    }
}
