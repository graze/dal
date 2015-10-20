<?php

namespace Graze\Dal\Adapter\DoctrineOrm\Persister;

use Doctrine\ORM\EntityManager;
use Graze\Dal\Adapter\ActiveRecord\Persister\AbstractPersister;
use Graze\Dal\Adapter\ActiveRecord\UnitOfWork;

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
	public function __construct($entityName, $recordName, UnitOfWork $unitOfWork, EntityManager $em)
	{
		parent::__construct($entityName, $recordName, $unitOfWork);
		$this->em = $em;
	}

	/**
     * @param array $criteria
     * @param object $entity
     * @param array $orderBy
     *
     * @return object
     */
    public function load(array $criteria, $entity = null, array $orderBy = null)
    {
	    $repository = $this->em->getRepository($this->getRecordName());
	    $record = $repository->findOneBy($criteria);

	    if (! $record) {
		    return null;
	    }

	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $entity = $mapper->toEntity($record, $entity);

	    return $record ? $this->persistImplicit($entity) : null;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return object[]
     */
    public function loadAll(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
	    $repository = $this->em->getRepository($this->getRecordName());
	    $records = $repository->findBy($criteria, $orderBy, $limit, $offset);

	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $entities = [];

	    foreach ($records as $record) {
		    $entity = $mapper->toEntity($record);
		    $this->unitOfWork->setEntityRecord($entity, $record);
		    $entities[] = $this->persistImplicit($entity);
	    }

	    return $entities;
    }

	/**
	 * @param int $id
	 * @param object $entity
	 *
	 * @return object
	 */
    public function loadById($id, $entity = null)
    {
	    $repository = $this->em->getRepository($this->getRecordName());
	    $record = $repository->find($id);

	    if (is_null($record)) {
		    return $record;
	    }

	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $entity = $mapper->toEntity($record, $entity);

	    $this->unitOfWork->setEntityRecord($entity, $record);

	    return $record ? $this->persistImplicit($entity) : null;
    }

	/**
	 * @param object $entity
	 * @return object
	 */
	protected function persistImplicit($entity)
	{
		$this->unitOfWork->persistByTrackingPolicy($entity);

		return $entity;
	}

    /**
     * @param object $entity
     */
    public function delete($entity)
    {
	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $record = $this->unitOfWork->getEntityRecord($entity);
	    $record = $mapper->fromEntity($entity, $record);

	    $this->unitOfWork->setEntityRecord($entity, null);

	    $this->em->remove($record);
    }

    /**
     * @param object $entity
     */
    public function refresh($entity)
    {
	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $data = $mapper->getEntityData($entity);

	    if (isset($data['id'])) {
		    $this->loadById($data['id'], $entity);
	    } else {
		    $record = $this->unitOfWork->getEntityRecord($entity);
		    $mapper->toEntity($record, $entity);
	    }
    }

    /**
     * @param object $entity
     */
    public function save($entity)
    {
	    $mapper = $this->unitOfWork->getMapper($this->getEntityName());
	    $record = $this->unitOfWork->getEntityRecord($entity);
	    $record = $mapper->fromEntity($entity, $record);

	    $this->unitOfWork->setEntityRecord($entity, $record);

	    $this->em->persist($record);
	    $this->em->flush();

	    $this->unitOfWork->removeEntityRecord($entity);

	    $mapper->toEntity($record, $entity);

	    $this->unitOfWork->setEntityRecord($entity, $record);
    }
}
