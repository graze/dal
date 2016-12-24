<?php

namespace Graze\Dal\Test\Entity;

class Order implements \Graze\Dal\Entity\EntityInterface, \Graze\Dal\Test\Entity\OrderInterface
{

    private $id = null;

    private $customer = null;

    private $products = null;

    /**
     * @param \Graze\Dal\Test\Entity\Customer $customer
     */
    public function __construct(\Graze\Dal\Test\Entity\Customer $customer)
    {
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
     * @return \Graze\Dal\Test\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Graze\Dal\Test\Entity\Customer $customer
     */
    public function setCustomer(\Graze\Dal\Test\Entity\Customer $customer)
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
     * @param \Graze\Dal\Test\Entity\Product $products
     */
    public function addProduct(\Graze\Dal\Test\Entity\Product $products)
    {
        $this->products->add($products);
    }

}
