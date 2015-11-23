<?php
namespace Graze\Dal\Adapter\ActiveRecord\Proxy;

use Closure;
use Doctrine\Common\Collections\Collection;
use Graze\Dal\Adapter\ActiveRecord\ConfigurationInterface;
use Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class ProxyFactory
{
    protected $config;
    protected $factory;
    protected $unitOfWork;
    protected $collectionClass = 'Doctrine\Common\Collections\ArrayCollection';

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function __construct(
        ConfigurationInterface $config,
        UnitOfWork $unitOfWork,
        LazyLoadingGhostFactory $factory
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @param string $class
     * @param callable $fn
     * @param string $collectionClass
     * @param array $args
     *
     * @return GhostObjectInterface
     */
    public function buildCollectionProxy($class, callable $fn, $collectionClass = null, array $args = [])
    {
        $collectionClassName = is_string($collectionClass) ? $collectionClass : $this->collectionClass;

        return $this->factory->createProxy($collectionClassName, function (Collection $proxy) use ($args, $class, $fn) {
            $proxy->setProxyInitializer(null);
            $records = call_user_func_array($fn, $args);

            if (is_array($records)) {
                $mapper = $this->unitOfWork->getMapper($class);
                $this->mapRecords($records, $proxy, $mapper);

                return true;
            }

            return false;
        });
    }

    /**
     * @param string $class
     * @param callable $fn
     * @param array $args
     *
     * @return GhostObjectInterface
     */
    public function buildEntityProxy($class, callable $fn, array $args = [])
    {
        return $this->factory->createProxy($class, function ($proxy) use ($args, $class, $fn) {
            $proxy->setProxyInitializer(null);
            $record = call_user_func_array($fn, $args);

            if ($record) {
                $mapper = $this->unitOfWork->getMapper($class);
                $this->mapRecord($record, $proxy, $mapper);

                return true;
            }

            return false;
        });
    }

    /**
     * @param object $record
     * @param object $entity
     * @param MapperInterface $mapper
     *
     * @return object
     */
    protected function mapRecord($record, $entity, MapperInterface $mapper)
    {
        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->persistByTrackingPolicy($entity);

        return $entity;
    }

    /**
     * @param object $record
     * @param Collection $collection
     * @param MapperInterface $mapper
     *
     * @return Collection
     */
    protected function mapRecords(array $records, Collection $collection, MapperInterface $mapper)
    {
        $collection->clear();

        foreach ($records as $record) {
            $entity = $mapper->toEntity($record);
            $this->unitOfWork->persistByTrackingPolicy($entity);

            $collection->add($entity);
        }

        return $collection;
    }
}
