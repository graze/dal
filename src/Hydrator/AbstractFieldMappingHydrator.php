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
use Graze\Dal\Exception\MissingConfigException;
use Zend\Stdlib\Hydrator\HydratorInterface;

abstract class AbstractFieldMappingHydrator implements HydratorInterface
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
     * @param  array|object $object
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

        foreach ($out as $field => $value) {
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
     * @param  array|object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $mapping = $this->getHydrationFieldMappings($object);

        foreach ($data as $field => $value) {
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

    /**
     * @param array|object $object
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

    /**
     * @param array|object $object
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
}
