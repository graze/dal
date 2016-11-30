<?php

namespace Graze\Dal\Adapter\Http\Persister;

use Graze\Dal\Adapter\Http\Exception\HttpMethodNotAllowedException;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;
use Graze\Dal\Persister\PersisterInterface;
use Graze\Dal\UnitOfWork\UnitOfWorkInterface;
use GuzzleHttp\ClientInterface;

abstract class AbstractPersister extends \Graze\Dal\Persister\AbstractPersister implements PersisterInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * AbstractPersister constructor.
     *
     * @param string $entityName
     * @param string $recordName
     * @param \Graze\Dal\UnitOfWork\UnitOfWorkInterface $unitOfWork
     * @param \Graze\Dal\Configuration\ConfigurationInterface $config
     * @param \GuzzleHttp\ClientInterface $client
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
     * @return \GuzzleHttp\ClientInterface
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function get($url)
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
    protected function post($url, array $body)
    {
        $this->checkHttpMethod('POST');
        return $this->request('POST', $url, $body);
    }

    /**
     * @param string $url
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws HttpMethodNotAllowedException
     */
    protected function put($url, array $body)
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
    protected function request($method, $url, array $body = null)
    {
        $url = $this->buildBaseUrl() . $url;
        $mapping = $this->config->getMapping($this->getEntityName());
        $options = array_key_exists('options', $mapping) ? $mapping['options'] : [];

        if ($body) {
            $options['json'] = $body;
        }

        return $this->getClient()->request($method, $url, $options);
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

        $host = $mapping['host'];
        $port = array_key_exists('port', $mapping) ? $mapping['port'] : 80;
        $url = $host . ':' . $port;

        return $url;
    }

    /**
     * @param string $method
     *
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
