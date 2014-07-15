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
namespace Graze\Dal\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;

abstract class ActiveRecordAdapter implements AdapterInterface
{
    protected $config;
    protected $repos = [];
    protected $unitOfWork;

    /**
     * @param Configuration $config
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
        $this->unitOfWork = $config->buildUnitOfWork($this);
    }

    /**
     * @param object $entity
     * @return string
     */
    public function getEntityName($entity)
    {
        return $this->config->getEntityName($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($name)
    {
        if (!$this->hasRepository($name)) {
            throw new UndefinedRepositoryException($name, __METHOD__);
        } elseif (!isset($this->repos[$name])) {
            $this->repos[$name] = $this->config->buildRepository($name, $this);
        }

        return $this->repos[$name];
    }

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRepository($name)
    {
        return (boolean) $this->config->getMapping($name);
    }

    /**
     * @{inheritdoc}
     */
    public function flush($entity = null)
    {
        if (null !== $entity) {
            $this->unitOfWork->commit($entity);
        } else {
            $this->unitOfWork->commit();
        }
    }

    /**
     * @{inheritdoc}
     */
    public function persist($entity)
    {
        $this->unitOfWork->persist($entity);
    }

    /**
     * @{inheritdoc}
     */
    public function refresh($entity)
    {
        $this->unitOfWork->refresh($entity);
    }
}
