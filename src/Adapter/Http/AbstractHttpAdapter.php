<?php

namespace Graze\Dal\Adapter\Http;

use Graze\Dal\Adapter\AbstractAdapter;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Configuration\ConfigurationInterface;
use GuzzleHttp\ClientInterface;

abstract class AbstractHttpAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param ConfigurationInterface $config
     */
    public function __construct(ClientInterface $client, ConfigurationInterface $config)
    {
        parent::__construct($config);
        $this->client = $client;
    }

    /**
     * @return ClientInterface
     */
    protected function getClient()
    {
        return $this->client;
    }
}
