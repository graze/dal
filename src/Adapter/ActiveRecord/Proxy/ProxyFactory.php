<?php
namespace Graze\Dal\Adapter\ActiveRecord\Proxy;

use Doctrine\Common\Collections\Collection;
use GeneratedHydrator\Configuration;
use Graze\Dal\Adapter\ActiveRecord\Mapper\MapperInterface;
use Graze\Dal\DalManager;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class ProxyFactory
{
    protected $factory;
    protected $unitOfWork;
    protected $collectionClass = 'Doctrine\Common\Collections\ArrayCollection';

    /**
     * @var DalManager
     */
    private $dalManager;

    /**
     * @param DalManager $dalManager
     * @param LazyLoadingGhostFactory $factory
     */
    public function __construct(DalManager $dalManager, LazyLoadingGhostFactory $factory)
    {
        $this->factory = $factory;
        $this->dalManager = $dalManager;
    }

    /**
     * @param string $class
     * @param callable $fn
     * @param string $collectionClass
     * @param array $args
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
     * @return GhostObjectInterface
     */
    public function buildEntityProxy($class, callable $fn, array $args = [])
    {
        return $this->factory->createProxy($class, function ($proxy) use ($args, $class, $fn) {
            $proxy->setProxyInitializer(null);
//            $record = call_user_func_array($fn, $args);
            $adapter = $this->dalManager->findAdapterByEntityName($class);
            $repository = $adapter->getRepository($class);
            $entity = $repository->find($fn());

            if ($entity) {
                $config = new Configuration($class);
                $hydratorClass = $config->createFactory()->getHydratorClass();
                $hydrator = new $hydratorClass();
                $extracted = $hydrator->extract($entity);
                $hydrator->hydrate($extracted, $proxy);
                $adapter->getUnitOfWork()->persistByTrackingPolicy($proxy);
                return true;
            }

//            if ($record) {
//                $mapper = $this->unitOfWork->getMapper($class);
//                $this->mapRecord($record, $proxy, $mapper);
//
//                return true;
//            }

            return false;
        });
    }

    /**
     * @param object $record
     * @param object $entity
     * @param MapperInterface $mapper
     * @return object
     */
    protected function mapRecord($record, $entity, MapperInterface $mapper)
    {
        $entity = $mapper->toEntity($record, $entity);
        $this->unitOfWork->persistByTrackingPolicy($entity);

        return $entity;
    }

    /**
     * @param array $records
     * @param Collection $collection
     * @param MapperInterface $mapper
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
