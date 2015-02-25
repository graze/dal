<?php

namespace Graze\Dal\NamingStrategy;

interface NamingStrategyInterface extends \Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface
{
    /**
     * @param object $record
     * @return bool
     */
    public function supports($record);
}
