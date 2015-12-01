<?php

namespace Graze\Dal\Adapter;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\DalManagerAwareInterface;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

abstract class AbstractAdapter implements AdapterInterface, DalManagerAwareInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @var array
     */
    private $repos = [];

    /**
     * @var UnitOfWorkInterface
     */
    private $unitOfWork;

    /**
     * @param ConfigurationInterface $config
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
        $this->unitOfWork = $config->buildUnitOfWork($this);
    }

    /**
     * @param DalManagerInterface $dm
     */
    public function setDalManager(DalManagerInterface $dm)
    {
        if ($this->config instanceof DalManagerAwareInterface) {
            $this->config->setDalManager($dm);
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRepository($name)
    {
        return (bool) $this->config->getMapping($name);
    }

    /**
     * @param string $name
     *
     * @return ObjectRepository
     * @throws UndefinedRepositoryException
     */
    public function getRepository($name)
    {
        if (! $this->hasRepository($name)) {
            throw new UndefinedRepositoryException($name, __METHOD__);
        } elseif (! array_key_exists($name, $this->repos)) {
            $this->repos[$name] = $this->config->buildRepository($name, $this);
        }

        return $this->repos[$name];
    }

    /**
     * @return UnitOfWorkInterface
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getEntityName($entity)
    {
        return $this->config->getEntityName($entity);
    }

    /**
     * @param object $entity
     */
    public function flush($entity = null)
    {
        if (null !== $entity) {
            return $this->unitOfWork->commit($entity);
        }

        return $this->unitOfWork->commit();
    }

    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->unitOfWork->persist($entity);
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
        $this->unitOfWork->refresh($entity);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->unitOfWork->remove($entity);
    }
}
