<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Graze\Dal\Test\Entity\Order;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Graze\Dal\Test\Entity\Order implements \Zend\Stdlib\Hydrator\HydratorInterface
{
    private $id = null;
    private $customer = null;
    private $products = null;
    function __construct()
    {
        $this->idWriter56698bc854327888642588 = \Closure::bind(function ($object, $value) {
            $object->id = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Order');
        $this->customerWriter56698bc854344170092972 = \Closure::bind(function ($object, $value) {
            $object->customer = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Order');
        $this->productsWriter56698bc854355001307916 = \Closure::bind(function ($object, $value) {
            $object->products = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Order');
    }
    function hydrate(array $data, $object)
    {
        $this->idWriter56698bc854327888642588->__invoke($object, $data['id']);
        $this->customerWriter56698bc854344170092972->__invoke($object, $data['customer']);
        $this->productsWriter56698bc854355001307916->__invoke($object, $data['products']);
        return $object;
    }
    function extract($object)
    {
        $data = (array) $object;
        return array('id' => $data[' Graze\\Dal\\Test\\Entity\\Order id'], 'customer' => $data[' Graze\\Dal\\Test\\Entity\\Order customer'], 'products' => $data[' Graze\\Dal\\Test\\Entity\\Order products']);
    }
}