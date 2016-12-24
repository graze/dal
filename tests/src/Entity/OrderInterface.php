<?php

namespace Graze\Dal\Test\Entity;

interface OrderInterface
{

    /**
     * @param \Graze\Dal\Test\Entity\Customer $customer
     */
    public function __construct(\Graze\Dal\Test\Entity\Customer $customer);
    /**
     * @return int
     */
    public function getId();
    /**
     * @return \Graze\Dal\Test\Entity\Customer
     */
    public function getCustomer();
    /**
     * @param \Graze\Dal\Test\Entity\Customer $customer
     */
    public function setCustomer(\Graze\Dal\Test\Entity\Customer $customer);
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts();
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $products
     */
    public function setProducts(\Doctrine\Common\Collections\ArrayCollection $products);
    /**
     * @param \Graze\Dal\Test\Entity\Product $products
     */
    public function addProduct(\Graze\Dal\Test\Entity\Product $products);

}
