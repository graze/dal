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
