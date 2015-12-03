<?php

namespace Graze\Dal\Adapter\Pdo\Persister;

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Persister\AbstractPersister;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;

class EntityPersister extends AbstractPersister
{
    /**
     * @var ExtendedPdo
     */
    private $db;

    /**
     * EntityPersister constructor.
     *
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWorkInterface $unitOfWork
     * @param ConfigurationInterface $config
     * @param ExtendedPdo $db
     */
    public function __construct(
        $entityName,
        $recordName,
        UnitOfWorkInterface $unitOfWork,
        ConfigurationInterface $config,
        ExtendedPdo $db
    ) {
        parent::__construct($entityName, $recordName, $unitOfWork, $config);
        $this->db = $db;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    protected function saveRecord($record)
    {
        if ($this->getRecordId($record)) {
            return $this->update($record);
        } else {
            unset($record['id']);
            return $this->insert($record);
        }
    }

    /**
     * @param array $record
     *
     * @return array
     */
    private function insert($record)
    {
        $cols = implode(',', array_keys($record));
        $vals = array_values($record);

        $stmt = "INSERT INTO `{$this->getRecordName()}` ({$cols}) VALUES (?)";
        $this->db->perform($stmt, [1 => $vals]);
        $record['id'] = $this->db->lastInsertId();

        return $record;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    private function update($record)
    {
        $cols = array_keys($record);
        $set = '';
        foreach ($cols as $col) {
            $set .= $col . '=' . ':' . $col . ',';
        }
        $set = rtrim($set, ',');

        $stmt = $this->db->prepare("UPDATE `{$this->getRecordName()}` SET {$set}");
        foreach ($record as $col => $value) {
            $stmt->bindValue($col, $value);
        }

        $this->db->exec($stmt);

        return $record;
    }

    /**
     * @param array $record
     */
    protected function deleteRecord($record)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->getRecordName()} WHERE `id` = :id");
        $stmt->bindValue('id', $this->getRecordId($record));

        $this->db->exec($stmt);
    }

    /**
     * @param array $record
     *
     * @return int
     */
    protected function getRecordId($record)
    {
        return array_key_exists('id', $record) ? $record['id'] : null;
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
        $where = $this->buildWhereClause($criteria);

        $stmt = "SELECT * FROM `{$this->getRecordName()}` WHERE {$where} LIMIT 1";

        return $this->db->fetchOne($stmt, $criteria);
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
        $where = $this->buildWhereClause($criteria);
        $where = $where ? ' WHERE ' . $where : '';

        $stmt = "SELECT * FROM `{$this->getRecordName()}` WHERE {$where}";

        if ($offset) {
            $stmt .= " OFFSET {$offset}";
        }

        if ($limit) {
            $stmt .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($stmt, $criteria);
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return object
     */
    protected function loadRecordById($id, $entity = null)
    {
        $stmt = "SELECT * FROM `{$this->getRecordName()}` WHERE `id` = :id";
        return $this->db->fetchOne($stmt, ['id' => $id]);
    }

    /**
     * @param array $criteria
     *
     * @return string
     */
    private function buildWhereClause(array $criteria)
    {
        $where = '';

        foreach ($criteria as $col => $value) {
            $where .= $col . '=' . ':' . $col . ',';
        }
        $where = rtrim($where, ',');
        return $where;
    }
}
