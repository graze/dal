<?php

namespace Graze\Dal\Adapter\Orm\Configuration;

use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\InvalidMappingException;

abstract class AbstractConfiguration extends \Graze\Dal\Configuration\AbstractConfiguration
{
    /**
     * @param string $entityName
     * @param ConfigurationInterface $config
     *
     * @return string
     */
    protected function getRecordName($entityName, ConfigurationInterface $config)
    {
        $mapping = $config->getMapping($entityName);

        if (! array_key_exists('record', $mapping)) {
            $message = sprintf('Invalid or missing value for "record" for "%s"', $entityName);
            throw new InvalidMappingException($message, __METHOD__);
        }

        return $mapping['record'];
    }
}
