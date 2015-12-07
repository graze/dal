<?php

namespace Graze\Dal\Adapter\Http\Rest\Persister;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\NotImplementedException;
use Graze\Dal\Exception\NotSupportedException;
use Graze\Dal\Persister\AbstractPersister;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use GuzzleHttp\ClientInterface;

class EntityPersister extends AbstractPersister
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param string $entityName
     * @param string $recordName
     * @param UnitOfWorkInterface $unitOfWork
     * @param ConfigurationInterface $config
     * @param ClientInterface $client
     */
    public function __construct(
        $entityName,
        $recordName,
        UnitOfWorkInterface $unitOfWork,
        ConfigurationInterface $config,
        ClientInterface $client
    ) {
        parent::__construct($entityName, $recordName, $unitOfWork, $config);
        $this->client = $client;
    }

    /**
     * @param object $record
     *
     * @return object|array
     * @throws NotImplementedException
     */
    protected function saveRecord($record)
    {
        throw new NotSupportedException('Saving is not supported', $this->unitOfWork->getAdapter());
    }

    /**
     * @param object $record
     * @throws NotImplementedException
     */
    protected function deleteRecord($record)
    {
        throw new NotSupportedException('Deleting is not supported', $this->unitOfWork->getAdapter());
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
        throw new NotSupportedException('Loading a single record by criteria is not supported', $this->unitOfWork->getAdapter());
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loadAllRecords(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $query = http_build_query($criteria);
        $url = $this->buildBaseUrl() . '?' . $query;

        $mapping = $this->config->getMapping($this->getEntityName());
        $options = array_key_exists('options', $mapping) ? $mapping['options'] : [];

        $response = $this->client->request('GET', $url, $options);

        $data = json_decode($response->getBody(), true);
        return array_key_exists('data', $data) ? $data['data'] : $data;
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loadRecordById($id, $entity = null)
    {
        $mapping = $this->config->getMapping($this->getEntityName());

        $url = $this->buildBaseUrl() . '/' . $id;
        $options = array_key_exists('options', $mapping) ? $mapping['options'] : [];

        $response = $this->client->request('GET', $url, $options);

        $data = json_decode($response->getBody(), true);
        return array_key_exists('data', $data) ? $data['data'] : $data;
    }

    /**
     * @return string
     */
    private function buildBaseUrl()
    {
        $mapping = $this->config->getMapping($this->getEntityName());

        $host = $mapping['host'];
        $port = array_key_exists('port', $mapping) ? $mapping['port'] : 80;
        $resource = $mapping['resource'];
        $url = $host . ':' . $port . '/' . $resource;

        return $url;
    }
}
