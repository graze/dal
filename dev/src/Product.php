<?php

namespace Graze\Dal\Dev;

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
