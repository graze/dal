<?php

namespace Graze\Dal\Adapter\Http\Rest\Persister;

use Graze\Dal\Adapter\Http\Rest\Exception\HttpMethodNotAllowedException;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;
use Graze\Dal\Persister\AbstractPersister;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @param array $record
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function saveRecord($record)
    {
        if ($this->getRecordId($record)) {
            // PUT
            $url = '/' . $this->getRecordId($record);
            return $this->handleResponse($this->put($url, ['json' => $record]));
        } else {
            // POST
            return $this->handleResponse($this->post('', ['json' => $record]));
        }
    }

    /**
     * @param array $record
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function deleteRecord($record)
    {
        $this->checkHttpMethod('DELETE');
        $url = $this->buildBaseUrl() . '/' . $this->getRecordId($record);
        $this->client->request('DELETE', $url);
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
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function loadRecord(array $criteria, $entity = null, array $orderBy = null)
    {
        return reset($this->loadAllRecords($criteria, $orderBy));
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function loadAllRecords(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $query = http_build_query($criteria);
        $url = '?' . $query;

        return $this->handleResponse($this->get($url));
    }

    /**
     * @param int $id
     * @param object $entity
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function loadRecordById($id, $entity = null)
    {
        $url = '/' . $id;

        return $this->handleResponse($this->get($url));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed
     */
    private function handleResponse(ResponseInterface $response)
    {
        $data = json_decode($response->getBody(), true);
        return array_key_exists('data', $data) ? $data['data'] : $data;
    }

    /**
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    private function get($url)
    {
        $this->checkHttpMethod('GET');
        return $this->request('GET', $url);
    }

    /**
     * @param string $url
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    private function post($url, array $body)
    {
        $this->checkHttpMethod('POST');
        return $this->request('POST', $url, $body);
    }

    /**
     * @param $url
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    private function put($url, array $body)
    {
        $this->checkHttpMethod('PUT');
        return $this->request('PUT', $url, $body);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request($method, $url, array $body = null)
    {
        $url = $this->buildBaseUrl() . $url;
        $mapping = $this->config->getMapping($this->getEntityName());
        $options = array_key_exists('options', $mapping) ? $mapping['options'] : [];

        if ($body) {
            $options['json'] = $body;
        }

        return $this->client->request($method, $url, $options);
    }

    /**
     * @return string
     * @throws MissingConfigException
     */
    private function buildBaseUrl()
    {
        $entityName = $this->getEntityName();
        $mapping = $this->config->getMapping($entityName);

        if (! array_key_exists('host', $mapping)) {
            throw new MissingConfigException($entityName, 'host');
        }

        if (! array_key_exists('resource', $mapping)) {
            throw new MissingConfigException($entityName, 'resource');
        }

        $host = $mapping['host'];
        $port = array_key_exists('port', $mapping) ? $mapping['port'] : 80;
        $resource = $mapping['resource'];
        $url = $host . ':' . $port . '/' . $resource;

        return $url;
    }

    /**
     * @param $method
     * @throws HttpMethodNotAllowedException
     */
    private function checkHttpMethod($method)
    {
        $mapping = $this->config->getMapping($this->getEntityName());

        if (array_key_exists('allowed_methods', $mapping)) {
            $methods = $mapping['allowed_methods'];
            if (! in_array($method, $methods)) {
                throw new HttpMethodNotAllowedException($method);
            }
        }
    }
}
