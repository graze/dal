<?php
namespace Graze\Dal\Proxy;

use Doctrine\Common\Collections\Collection;
use GeneratedHydrator\Configuration;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Relationship\ResolverInterface;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class ProxyFactory implements ProxyFactoryInterface
{
    protected $factory;
    protected $unitOfWork;
    protected $collectionClass = 'Doctrine\Common\Collections\ArrayCollection';

    /**
     * @var ResolverInterface
     */
    private $relationshipResolver;

    /**
     * @var DalManagerInterface
     */
    private $dm;

    /**
     * @param DalManagerInterface $dm
     * @param ResolverInterface $relationshipResolver
     * @param LazyLoadingGhostFactory $factory
     */
    public function __construct(DalManagerInterface $dm, ResolverInterface $relationshipResolver, LazyLoadingGhostFactory $factory)
    {
        $this->factory = $factory;
        $this->relationshipResolver = $relationshipResolver;
        $this->dm = $dm;
    }

    /**
     * @param string $class
     * @param callable $id
     * @param array $config
     * @param string $collectionClass
     *
     * @return GhostObjectInterface
     */
    public function buildCollectionProxy($class, callable $id, array $config, $collectionClass = null)
    {
        $collectionClassName = is_string($collectionClass) ? $collectionClass : $this->collectionClass;

        return $this->factory->createProxy($collectionClassName, function (Collection $proxy) use ($class, $id, $config) {
            $proxy->setProxyInitializer(null);

            $entities = $this->relationshipResolver->resolve($class, $id(), $config);

            if ($entities) {
                $proxy->clear();
                $adapter = $this->dm->findAdapterByEntityName($class);
                foreach ($entities as $entity) {
                    $adapter->getUnitOfWork()->persistByTrackingPolicy($entity);
                    $proxy->add($entity);
                }
                return true;
            }

            return false;
        });
    }

    /**
     * @param string $class
     * @param callable $id
     * @param array $config
     *
     * @return GhostObjectInterface
     */
    public function buildEntityProxy($class, callable $id, array $config)
    {
        return $this->factory->createProxy($class, function ($proxy) use ($class, $id, $config) {
            $proxy->setProxyInitializer(null);

            $entities = $this->relationshipResolver->resolve($class, $id(), $config);
            $entity = reset($entities);

            if ($entity) {
                $adapter = $this->dm->findAdapterByEntityName($class);
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
