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
namespace Graze\Dal\Adapter\ActiveRecord;

use Doctrine\Common\Persistence\ObjectRepository;
use Graze\Dal\Adapter\ActiveRecordAdapter;

/**
 * @deprecated - DAL 0.x
 */
class EntityRepository implements ObjectRepository
{
    /**
     * @var \Graze\Dal\Adapter\ActiveRecordAdapter
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param string $entityName
     * @param ActiveRecordAdapter $adapter
     */
    public function __construct($entityName, ActiveRecordAdapter $adapter)
    {
        $this->adapter = $adapter;
        $this->entityName = $entityName;
    }

    /**
     * @param mixed $id
     *
     * @return mixed
     */
    public function find($id)
    {
        $unitOfWork = $this->adapter->getUnitOfWork();

        return $unitOfWork->getPersister($this->entityName)->loadById($id);
    }

    /**
     * @return array
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
     * @return mixed
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
     * @return mixed
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
