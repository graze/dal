<?php

namespace Graze\Dal\Dev;

class Order implements \Graze\Dal\Entity\EntityInterface
{

    private $id = null;

    private $price = null;

    private $customer = null;

    private $products = null;

    /**
     * @param float $price
     * @param \Graze\Dal\Dev\Customer $customer
     */
    public function __construct($price, \Graze\Dal\Dev\Customer $customer)
    {
        $this->price = (float) $price;
        $this->customer = $customer;
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
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

    /**
     * @return \Graze\Dal\Dev\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Graze\Dal\Dev\Customer $customer
     */
    public function setCustomer(\Graze\Dal\Dev\Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $products
     */
    public function setProducts(\Doctrine\Common\Collections\ArrayCollection $products)
    {
        $this->products = $products;
    }

    /**
     * @param \Graze\Dal\Dev\Product $products
     */
    public function addProduct(\Graze\Dal\Dev\Product $products)
    {
        $this->products->add($products);
    }

}
