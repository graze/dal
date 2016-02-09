<?php
namespace Graze\Dal\Hydrator;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Entity\EntityInterface;
use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Proxy\ProxyFactoryInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class RelationshipProxyHydrator implements HydratorInterface
{
    protected $config;
    protected $next;
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
     * {@inheritdoc}
     */
    public function extract($object)
    {
        $out = [];
        if ($this->next) {
            $out += $this->next->extract($object);
        }

        $mapping = $this->formatMapping($this->config->getMapping($this->config->getEntityName($object)));

        foreach ($out as $field => $value) {
            if (is_object($value)) {
                $map = $mapping[$field];
                if ($map['type'] !== 'manyToMany') {
                    if (! $value instanceof EntityInterface) {
                        throw new \InvalidArgumentException('Entity ' . get_class($relatedEntity) . ' must implement Graze\Dal\Entity\EntityInterface');
                    }
                    unset($out[$field]);
                    $out[$map['localKey']] = $value->getId();
                }
            }
        }

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        $entityName = $this->config->getEntityName($object);
        $mapping = $this->formatMapping($this->config->getMapping($entityName) ?: []);

        $out = array_map(function ($map) use ($data, $object, $entityName) {
            $foreignEntity = $map['entity'];

            if ($map['collection']) {
                $collectionClass = is_string($map['collection']) ? $map['collection'] : null;
                $id = function () use ($map, $data, $object) {
                    return $map['type'] === 'manyToMany' ? (int) $data['id'] : $object->getId();
                };
                return $this->proxyFactory->buildCollectionProxy($entityName, $foreignEntity, $id, $map, $collectionClass);
            } else {
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
