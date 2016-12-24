<?php

namespace Graze\Dal\Test\Entity;

interface ProductInterface
{

    /**
     * @param string $name
     * @param float $price
     */
    public function __construct($name, $price);
    /**
     * @return int
     */
    public function getId();
    /**
     * @param string $name
     */
    public function setName($name);
    /**
     * @return string
     */
    public function getName();
    /**
     * @param float $price
     */
    public function setPrice($price);
    /**
     * @return float
     */
    public function getPrice();

}
