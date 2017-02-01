<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Orm\DoctrineOrm\Persister;

use Doctrine\ORM\EntityManager;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Persister\AbstractPersister;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class EntityPersister extends AbstractPersister
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWorkInterface $unitOfWork
     * @param ConfigurationInterface $config
     * @param EntityManager $em
     */
    public function __construct(
        $entityName,
        $recordName,
        UnitOfWorkInterface $unitOfWork,
        ConfigurationInterface $config,
        EntityManager $em
    ) {
        parent::__construct($entityName, $recordName, $unitOfWork, $config);
        $this->em = $em;
    }

    /**
     * @param object $record
     *
     * @return array|object
     */
    protected function saveRecord($record)
    {
        $this->em->persist($record);
        $this->em->flush();

        return $record;
    }

    /**
     * @param object $record
     */
    protected function deleteRecord($record)
    {
        $this->em->remove($record);
    }

    /**
     * @return int
     */
    protected function getRecordId($record)
    {
        return $record->getId();
    }

    /**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    protected function loadRecord(array $criteria, $entity = null, array $orderBy = null)
    {
        $repository = $this->em->getRepository($this->getRecordName());
        return $repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    protected function loadAllRecords(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $repository = $this->em->getRepository($this->getRecordName());
        return $repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return object
     */
    protected function loadRecordById($id, $entity = null)
    {
        $repository = $this->em->getRepository($this->getRecordName());
        return $repository->find($id);
    }
}
