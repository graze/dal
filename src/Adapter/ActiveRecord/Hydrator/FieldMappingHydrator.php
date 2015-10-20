<?php

namespace Graze\Dal\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class FieldMappingHydrator implements HydratorInterface
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
	 * @param  object $object
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
			$this->next->hydrate($data, $object);
		}

		return $object;
	}

	/**
	 * @param object $object
	 *
	 * @return array
	 */
	protected function getHydrationFieldMappings($object)
	{
		$mapping = $this->config->getMapping($this->config->getEntityName($object));
		$mappings = [];

		if (! $mapping) {
			return [];
		}

		foreach ($mapping['fields'] as $field => $config) {
			$mappings[$config['mapsTo']] = $field;
		}

		return $mappings;
	}

	/**
	 * @param mixed $object
	 *
	 * @return array
	 */
	protected function getExtractionFieldMappings($object)
	{
		$mapping = $this->config->getMapping($this->config->getEntityName($object));
		$mappings = [];

		if (! $mapping) {
			return [];
		}

		foreach ($mapping['fields'] as $field => $config) {
			$mappings[$field] = $config['mapsTo'];
		}

		return $mappings;
	}
}
