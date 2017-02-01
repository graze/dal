<?php

namespace Graze\Dal\Adapter\Orm\Configuration;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\MissingConfigException;

abstract class AbstractConfiguration extends \Graze\Dal\Configuration\AbstractConfiguration
{
    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     * @throws MissingConfigException
     */
    protected function getRecordName($entityName, ConfigurationInterface $config)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            throw new MissingConfigException($entityName, 'record');
        }

        return $mapping['record'];
    }
}
