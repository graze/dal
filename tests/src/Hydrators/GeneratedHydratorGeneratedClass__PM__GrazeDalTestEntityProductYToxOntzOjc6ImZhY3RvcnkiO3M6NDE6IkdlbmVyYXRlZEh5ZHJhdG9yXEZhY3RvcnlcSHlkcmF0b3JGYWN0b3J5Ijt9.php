<?php

namespace GeneratedHydratorGeneratedClass\__PM__\Graze\Dal\Test\Entity\Product;

class YToxOntzOjc6ImZhY3RvcnkiO3M6NDE6IkdlbmVyYXRlZEh5ZHJhdG9yXEZhY3RvcnlcSHlkcmF0b3JGYWN0b3J5Ijt9 extends \Graze\Dal\Test\Entity\Product implements \Zend\Stdlib\Hydrator\HydratorInterface
{
    private $id = null;
    private $name = null;
    private $price = null;
    function __construct()
    {
        $this->idWriter56698bc858f20160176310 = \Closure::bind(function ($object, $value) {
            $object->id = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Product');
        $this->nameWriter56698bc858f3c174534168 = \Closure::bind(function ($object, $value) {
            $object->name = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Product');
        $this->priceWriter56698bc858f4e171191168 = \Closure::bind(function ($object, $value) {
            $object->price = $value;
        }, null, 'Graze\\Dal\\Test\\Entity\\Product');
    }
    function hydrate(array $data, $object)
    {
        $this->idWriter56698bc858f20160176310->__invoke($object, $data['id']);
        $this->nameWriter56698bc858f3c174534168->__invoke($object, $data['name']);
        $this->priceWriter56698bc858f4e171191168->__invoke($object, $data['price']);
        return $object;
    }
    function extract($object)
    {
        $data = (array) $object;
        return array('id' => $data[' Graze\\Dal\\Test\\Entity\\Product id'], 'name' => $data[' Graze\\Dal\\Test\\Entity\\Product name'], 'price' => $data[' Graze\\Dal\\Test\\Entity\\Product price']);
    }
}