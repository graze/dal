<?php

namespace Graze\Dal\Adapter\Orm\Hydrator;

use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Proxy\ProxyFactory;
use Zend\Stdlib\Hydrator\HydratorInterface;

abstract class AbstractHydratorFactory implements HydratorFactoryInterface
{
	/**
	 * @var ConfigurationInterface
	 */
	protected $config;

	/**
	 * @var ProxyFactory
	 */
	private $proxyFactory;

	/**
	 * @param ConfigurationInterface $config
	 * @param ProxyFactory $proxyFactory
	 */
	public function __construct(ConfigurationInterface $config, ProxyFactory $proxyFactory)
	{
		$this->config = $config;
		$this->proxyFactory = $proxyFactory;
	}

	/**
	 * @param object $entity
	 *
	 * @return HydratorInterface
	 */
	abstract protected function buildDefaultEntityHydrator($entity);

	/**
	 * @param object $record
	 *
	 * @return HydratorInterface
	 */
	abstract protected function buildDefaultRecordHydrator($record);

	/**
	 * @param object $entity
	 *
	 * @return HydratorInterface
	 */
	public function buildEntityHydrator($entity)
	{
		$defaultHydrator = $this->buildDefaultEntityHydrator($entity);

		return new MethodProxyHydrator(
			$this->config,
			$this->proxyFactory,
			new FieldMappingHydrator($this->config, $defaultHydrator)
		);
	}

	/**
	 * @param object $record
	 *
	 * @return HydratorInterface
	 */
	public function buildRecordHydrator($record)
	{
		$defaultHydrator = $this->buildDefaultRecordHydrator($record);
		return new FieldMappingHydrator($this->config, $defaultHydrator);
	}
}
