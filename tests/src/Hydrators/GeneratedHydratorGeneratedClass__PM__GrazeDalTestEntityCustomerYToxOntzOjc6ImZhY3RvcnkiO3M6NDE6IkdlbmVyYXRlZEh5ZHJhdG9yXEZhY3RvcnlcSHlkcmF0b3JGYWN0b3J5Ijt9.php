<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Graze\Dal\Test\Entity\Customer;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Graze\Dal\Test\Entity\Customer implements \Zend\Stdlib\Hydrator\HydratorInterface
{
    private $id = null;
    private $firstName = null;
    private $lastName = null;
    private $orders = null;
    function __construct()
    {
        $this->idWriter56698bc830ad7926739581 = \Closure::bind(function ($object, $value) {
            $object->id = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Customer');
        $this->firstNameWriter56698bc830b1f594911530 = \Closure::bind(function ($object, $value) {
            $object->firstName = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Customer');
        $this->lastNameWriter56698bc830b34324442353 = \Closure::bind(function ($object, $value) {
            $object->lastName = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Customer');
        $this->ordersWriter56698bc830b48014584224 = \Closure::bind(function ($object, $value) {
            $object->orders = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Customer');
    }
    function hydrate(array $data, $object)
    {
        $this->idWriter56698bc830ad7926739581->__invoke($object, $data['id']);
        $this->firstNameWriter56698bc830b1f594911530->__invoke($object, $data['firstName']);
        $this->lastNameWriter56698bc830b34324442353->__invoke($object, $data['lastName']);
        $this->ordersWriter56698bc830b48014584224->__invoke($object, $data['orders']);
        return $object;
    }
    function extract($object)
    {
        $data = (array) $object;
        return array('id' => $data[' Graze\\Dal\\Test\\Entity\\Customer id'], 'firstName' => $data[' Graze\\Dal\\Test\\Entity\\Customer firstName'], 'lastName' => $data[' Graze\\Dal\\Test\\Entity\\Customer lastName'], 'orders' => $data[' Graze\\Dal\\Test\\Entity\\Customer orders']);
    }
}