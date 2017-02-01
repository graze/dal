<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\AdapterInterface;

class EntityRepository implements ObjectRepository
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param string $entityName
     * @param AdapterInterface $adapter
     */
    public function __construct($entityName, AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->entityName = $entityName;
    }

    /**
     * @param int $id
     *
     * @return object
     */
    public function find($id)
    {
        $unitOfWork = $this->adapter->getUnitOfWork();

        return $unitOfWork->getPersister($this->entityName)->loadById($id);
    }

    /**
     * @return array|\object[]
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return \object[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $unitOfWork = $this->adapter->getUnitOfWork();

        return $unitOfWork->getPersister($this->entityName)->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return object
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $unitOfWork = $this->adapter->getUnitOfWork();

        return $unitOfWork->getPersister($this->entityName)->load($criteria, null, $orderBy);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->entityName;
    }
}
