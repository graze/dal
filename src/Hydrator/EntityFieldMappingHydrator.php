<?php

namespace Graze\Dal\Hydrator;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;
use Zend\Stdlib\Hydrator\HydratorInterface;

class EntityFieldMappingHydrator implements HydratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @var HydratorInterface
     */
    private $next;

    /**
     * @param ConfigurationInterface $config
     * @param HydratorInterface $next
     */
    public function __construct(ConfigurationInterface $config, HydratorInterface $next = null)
    {
        $this->config = $config;
        $this->next = $next;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        $out = [];
        if ($this->next) {
            $out += $this->next->extract($object);
        }

        $mapping = $this->getExtractionFieldMappings($object);
        $fields = $this->getFields($object);

        foreach ($out as $field => $value) {
//            if ($fields && ! array_key_exists($field, $fields)) {
//                unset($out[$field]);
//                continue;
//            }
            if (array_key_exists($field, $mapping)) {
                unset($out[$field]);
                $out[$mapping[$field]] = $value;
            }
        }

        return $out;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $mapping = $this->getHydrationFieldMappings($object);
        $fields = $this->getFields($object);

        foreach ($data as $field => $value) {
//            if ($fields && ! array_key_exists($field, $fields)) {
////                unset($data[$field]);
//                continue;
//            }
            if (array_key_exists($field, $mapping)) {
                unset($data[$field]);
                $data[$mapping[$field]] = $value;
            }
        }

        if ($this->next) {
            $object = $this->next->hydrate($data, $object);
        }

        return $object;
    }

    private function getFields($object)
    {
        $mapping = $this->config->getMapping($this->config->getEntityName($object));

        if (! $mapping) {
            return [];
        }

        if (array_key_exists('fields', $mapping)) {
            return $mapping['fields'];
        }

        return [];
    }

    /**
     * @param object $object
     *
     * @return array
     * @throws MissingConfigException
     */
    protected function getHydrationFieldMappings($object)
    {
        $entityName = $this->config->getEntityName($object);
        $mapping = $this->config->getMapping($entityName);
        $mappings = [];

        if (! $mapping) {
            return [];
        }

        if (! array_key_exists('fields', $mapping)) {
            return [];
        }

        foreach ($mapping['fields'] as $field => $config) {

            if (! array_key_exists('mapsTo', $config)) {
                throw new MissingConfigException($entityName, 'fields.' . $field . '.mapsTo');
            }

            $mappings[$config['mapsTo']] = $field;
        }

        return $mappings;
    }

    /**
     * @param mixed $object
     *
     * @return array
     * @throws MissingConfigException
     */
    protected function getExtractionFieldMappings($object)
    {
        $entityName = $this->config->getEntityName($object);
        $mapping = $this->config->getMapping($entityName);
        $mappings = [];

        if (! $mapping) {
            return [];
        }

        if (! array_key_exists('fields', $mapping)) {
            return [];
        }

        foreach ($mapping['fields'] as $field => $config) {

            if (! array_key_exists('mapsTo', $config)) {
                throw new MissingConfigException($entityName, 'fields.' . $field . '.mapsTo');
            }

            $mappings[$field] = $config['mapsTo'];
        }

        return $mappings;
    }
}
