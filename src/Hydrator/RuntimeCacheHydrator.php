<?php

namespace Graze\Dal\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class RuntimeCacheHydrator implements HydratorInterface
{
    /**
     * @var HydratorInterface
     */
    private $next;

    private $hydrated = [];

    /**
     * @param HydratorInterface $next
     */
    public function __construct(HydratorInterface $next)
    {
        $this->next = $next;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        return $this->next->extract($object);
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
        $hash = $this->getHydrationHash($data, $object);

        if (! array_key_exists($hash, $this->hydrated)) {
            $this->hydrated[$hash] = $this->next->hydrate($data, $object);
        }

        return $this->hydrated[$hash];
    }

    /**
     * @param array $data
     * @param $object
     *
     * @return string
     */
    private function getHydrationHash(array $data, $object)
    {
        sort($data);
        if (is_object($object)) {
            $name = serialize($data) . spl_object_hash($object);
            return md5($name);
        } elseif (is_array($object)) {
            $name = serialize($data) . serialize($object);
            return md5($name);
        }
    }
}
