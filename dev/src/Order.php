<?php

namespace Graze\Dal\Dev;

class Order
{
	private $id;
	private $customer;
	private $price;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
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
		return $this->price;
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}
}
