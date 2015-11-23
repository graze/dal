<?php

namespace Graze\Dal\Adapter\Orm\DoctrineOrm\Persister;

use Doctrine\ORM\EntityManager;
use Graze\Dal\Adapter\Orm\ConfigurationInterface;
use Graze\Dal\Adapter\Orm\Persister\AbstractPersister;
use Graze\Dal\Adapter\Orm\UnitOfWork;

class EntityPersister extends AbstractPersister
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWork $unitOfWork
     * @param EntityManager $em
     */
    public function __construct(
        $entityName,
        $recordName,
        UnitOfWork $unitOfWork,
        ConfigurationInterface $config,
        EntityManager $em
    ) {
        parent::__construct($entityName, $recordName, $unitOfWork, $config);
        $this->em = $em;
    }

    /**
     * @param object $record
     */
    protected function saveRecord($record)
    {
        $this->em->persist($record);
        $this->em->flush();
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
