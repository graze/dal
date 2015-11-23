<?php
namespace Graze\Dal\Adapter\Orm\Proxy;

use Doctrine\Common\Collections\Collection;
use GeneratedHydrator\Configuration;
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
     *
     * @return GhostObjectInterface
     */
    public function buildCollectionProxy($class, callable $fn, $collectionClass = null, array $args = [])
    {
        $collectionClassName = is_string($collectionClass) ? $collectionClass : $this->collectionClass;

        return $this->factory->createProxy($collectionClassName, function (Collection $proxy) use ($args, $class, $fn) {
            $proxy->setProxyInitializer(null);

            // find all the $class entities for criteria returned by $fn()
            $adapter = $this->dalManager->findAdapterByEntityName($class);
            $repository = $adapter->getRepository($class);
            $entities = $repository->findBy($fn());

            if ($entities) {
                $proxy->clear();
                foreach ($entities as $entity) {
                    $adapter->getUnitOfWork()->persistByTrackingPolicy($entity);
                    $proxy->add($entity);
                }
                return true;
            }

            return false;
        });
    }

    public function buildManyToManyCollectionProxy(
        $foreignEntityName,
        $localEntityName,
        $pivotTableName,
        $foreignKey,
        $localKey,
        callable $fn,
        $collectionClass
    ) {
        $collectionClassName = is_string($collectionClass) ? $collectionClass : $this->collectionClass;

        return $this->factory->createProxy($collectionClassName, function (Collection $proxy) use (
            $foreignEntityName,
            $localEntityName,
            $pivotTableName,
            $foreignKey,
            $localKey,
            $fn
        ) {
            $proxy->setProxyInitializer(null);

            $sql = "SELECT {$foreignKey} FROM {$pivotTableName} WHERE {$localKey} = ?";

            // find all the $class entities using the many to many config
            $foreignAdapter = $this->dalManager->findAdapterByEntityName($foreignEntityName);
            $foreignRepository = $foreignAdapter->getRepository($foreignEntityName);
            $localAdapter = $this->dalManager->findAdapterByEntityName($localEntityName);
            $foreignIds = array_values($localAdapter->fetchCol($sql, [$fn()]));

            $entities = [];
            foreach ($foreignIds as $id) {
                $entities[] = $foreignRepository->find($id);
            }

            if (is_array($entities) && count($entities) > 0) {
                $proxy->clear();
                foreach ($entities as $entity) {
                    $foreignAdapter->getUnitOfWork()->persistByTrackingPolicy($entity);
                    $proxy->add($entity);
                }
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

            return false;
        });
    }
}
