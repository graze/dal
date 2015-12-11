<?php

namespace Graze\Dal\Test\Entity;

class Product implements \Graze\Dal\Entity\EntityInterface
{

    private $id = null;

    private $name = null;

    private $price = null;

    /**
     * @param string $name
     * @param string $price
     */
    public function __construct($name, $price)
    {
        $this->name = (string) $name;
        $this->price = $price;
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
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

}
