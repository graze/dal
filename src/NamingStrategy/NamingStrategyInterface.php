<?php

namespace Graze\Dal\NamingStrategy;

interface NamingStrategyInterface extends \Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface
{
    /**
     * @param string|object $object
     * @return bool
     */
    public function supports($object);
}
