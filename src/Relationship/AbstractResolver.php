<?php

namespace Graze\Dal\Relationship;

use Graze\Dal\DalManagerInterface;

abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @var DalManagerInterface
     */
    protected $dm;

    /**
     * @param DalManagerInterface $dm
     */
    public function __construct(DalManagerInterface $dm)
    {
        $this->dm = $dm;
    }
}
