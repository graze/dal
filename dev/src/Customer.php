<?php

namespace Graze\Dal\Dev;

class Customer implements \Graze\Dal\Entity\EntityInterface
{

    private $id = null;

    private $firstName = null;

    private $lastName = null;

    private $orders = null;

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $orders
     */
    public function setOrders(\Doctrine\Common\Collections\ArrayCollection $orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param \Graze\Dal\Dev\Order $orders
     */
    public function addOrder(\Graze\Dal\Dev\Order $orders)
    {
        $this->orders->add($orders);
    }

}
