<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\ActiveRecord;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\ActiveRecordAdapter;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use Graze\Dal\Exception\InvalidRepositoryException;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    protected $mapping;
    protected $trackingPolicy;

    /**
     * @param array $mapping
     * @param integer $trackingPolicy
     */
    public function __construct(array $mapping, $trackingPolicy = UnitOfWork::POLICY_IMPLICIT)
    {
        $this->mapping = $mapping;
        $this->trackingPolicy = $trackingPolicy;
    }

    /**
     * @param string $entityName
     * @param string $recordName
     * @return MapperInterface
     */
    abstract protected function buildDefaultMapper($entityName, $recordName);

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $uow
     * @return PersisterInterface
     */
    abstract protected function buildDefaultPersister($entityName, $recordName, UnitOfWork $uow);

    /**
     * {@inheritdoc}
     */
    public function buildMapper($name)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $this->buildDefaultMapper($name, $mapping['record'], $this->getHydratorFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function buildPersister($name, UnitOfWork $uow)
    {
        $mapping = $this->getMapping($name);

        if (!isset($mapping['record'])) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $name);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $this->buildDefaultPersister($name, $mapping['record'], $uow);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRepository($name, ActiveRecordAdapter $adapter)
    {
        $mapping = $this->getMapping($name);

        if (isset($mapping['repository'])) {
            $class = $mapping['repository'];
            $repo = new $class($name, $adapter);

            if (!$repo instanceof ObjectRepository) {
                throw new InvalidRepositoryException($repo, __METHOD__);
            }
        } else {
            $repo = $this->buildDefaultRepository($name, $adapter);
        }

        return $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUnitOfWork(ActiveRecordAdapter $adapter)
    {
        return new UnitOfWork($adapter, $this, $this->trackingPolicy);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName($entity)
    {
        return get_class($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping($name)
    {
        return isset($this->mapping[$name]) ? $this->mapping[$name] : null;
    }

    /**
     * @param string $name
     * @param ActiveRecordAdapter $adapter
     * @return EntityRepository
     */
    protected function buildDefaultRepository($name, ActiveRecordAdapter $adapter)
    {
        return new EntityRepository($name, $adapter);
    }
}
