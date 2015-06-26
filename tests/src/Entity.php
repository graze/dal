<?php

namespace Graze\Dal\Test;

class Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }
}
