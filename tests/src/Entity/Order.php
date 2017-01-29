<?php

namespace Graze\Dal\Test\Entity;

/**
 * This is a generated entity that is managed by DAL, manual changes to this entity
 * will be lost if the generate command is ran again. Changes should be made to the
 * config that is managing this entity and the generate command ran.
 */
class Order implements \Graze\Dal\Entity\EntityInterface
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
