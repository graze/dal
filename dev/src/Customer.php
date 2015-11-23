<?php

namespace Graze\Dal\Dev;

use Doctrine\Common\Collections\ArrayCollection;
use Graze\Dal\Entity\EntityInterface;

class Customer implements EntityInterface
{
    private $id;
    private $firstName;
    private $lastName;
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
