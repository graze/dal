<?php

namespace Graze\Dal\Adapter\Http\Rest;

use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Adapter\Http\AbstractHttpAdapter;
use Graze\Dal\Adapter\Http\Rest\Configuration\Configuration;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Yaml\Parser;

class RestAdapter extends AbstractHttpAdapter implements AdapterInterface
{
    /**
     * @param ClientInterface $client
     * @param array $configPath
     *
     * @return static
     */
    public static function factory(ClientInterface $client, $configPath)
    {
        $parser = new Parser();
        $config = $parser->parse(file_get_contents($configPath));
        return new static($client, new Configuration($client, $config));
    }
}
