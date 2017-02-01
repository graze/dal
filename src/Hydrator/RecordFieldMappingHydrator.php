<?php

namespace Graze\Dal\Hydrator;

use Graze\Dal\Exception\MissingConfigException;
use Zend\Stdlib\Hydrator\HydratorInterface;

class RecordFieldMappingHydrator extends AbstractFieldMappingHydrator implements HydratorInterface
{
    /**
     * @param array|object $object
     *
     * @return array
     * @throws MissingConfigException
     */
    protected function getHydrationFieldMappings($object)
    {
        if (! is_object($object)) {
            return [];
        }

        return parent::getHydrationFieldMappings($object);
    }

    /**
     * @param array|object $object
     *
     * @return array
     * @throws MissingConfigException
     */
    protected function getExtractionFieldMappings($object)
    {
        if (! is_object($object)) {
            return [];
        }

        return parent::getExtractionFieldMappings($object);
    }
}
