<?php

namespace Graze\Dal\Test\Entity;

/**
 * This is a generated entity that is managed by DAL, manual changes to this entity
 * will be lost if the generate command is ran again. Changes should be made to the
 * config that is managing this entity and the generate command ran.
 */
class Product implements \Graze\Dal\Entity\EntityInterface
{

    private $id = null;

    private $name = null;

    private $price = null;

    /**
     * @param string $name
     * @param float $price
     */
    public function __construct($name, $price)
    {
        $this->name = (string) $name;
        $this->price = (float) $price;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string) $this->name;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = (float) $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->price;
    }

}
