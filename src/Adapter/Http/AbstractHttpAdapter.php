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
