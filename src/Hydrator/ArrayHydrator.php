<?php

namespace Graze\Dal\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class ArrayHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        return (array) $object;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $object = $data;
        return $object;
    }
}
