<?php
namespace Graze\Dal\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Exception\InvalidMappingException;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\Proxy\ProxyFactory;
use Zend\Stdlib\Hydrator\HydratorInterface;

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
        $out = [];
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
        $entityName = $this->config->getEntityName($object);
        $mapping = $this->formatMapping($this->config->getMapping($entityName) ?: []);

        $out = array_map(function ($map) use ($object) {
            $args = $map['args'];
            $entity = $map['entity'];
            $callable = function () use ($object, $map) {
                return (int) $object->{$map['localKey']};
            };
            $callable = \Closure::bind($callable, null, $object);

            if ($map['collection']) {
                $collectionClass = is_string($map['collection']) ? $map['collection'] : null;
                return $this->proxyFactory->buildCollectionProxy($entity, $callable, $collectionClass, $args);
            } else {
                return $this->proxyFactory->buildEntityProxy($entity, $callable, $args);
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
     * @return array
     */
    protected function formatMapping(array $mapping)
    {
        if (!isset($mapping['related']) || !is_array($mapping['related'])) {
            return [];
        }

        return array_map(function ($map) {
            $out = [
                'args' => isset($map['args']) ? $map['args'] : [],
                'entity' => isset($map['entity']) ? $map['entity'] : null,
                'type' => isset($map['type']) ? $map['type'] : null,
                'collection' => isset($map['collection']) ? $map['collection'] : false,
                'localKey' => isset($map['localKey']) ? $map['localKey'] : null,
            ];

            if (!$out['entity'] || !$out['type']) {
                $message = 'Relationship mapping must contain "entity" and "type" values';
                throw new InvalidMappingException($message, __METHOD__);
            }

            return $out;
        }, $mapping['related']);
    }
}
