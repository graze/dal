<?php

namespace Graze\Dal\Dev;

use Graze\Dal\Entity\EntityInterface;

class Order implements EntityInterface
{
	private $orderId;
	private $customer;
	private $orderPrice;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->orderId;
	}

	/**
	 * @return Customer
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * @param Customer $customer
	 */
	public function setCustomer(Customer $customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->orderPrice;
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
		$this->orderPrice = $price;
	}
}
