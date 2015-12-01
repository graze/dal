<?php

namespace Graze\Dal;

interface DalManagerAwareInterface
{
    /**
     * @param DalManagerInterface $dm
     */
    public function setDalManager(DalManagerInterface $dm);
}
