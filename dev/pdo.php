<?php

require __DIR__ . '/../vendor/autoload.php';

$dm = new \Graze\Dal\DalManager();

$pdo = new \Aura\Sql\ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');
$dm->set('pdo', \Graze\Dal\Adapter\Pdo\PdoAdapter::factory($pdo, __DIR__ . '/config/pdo.yml'));

$toy = new \Graze\Dal\Dev\Product('Toy', 1.99);

$dm->persist($toy);
$dm->flush();

$customer = new \Graze\Dal\Dev\Customer('Will', 'Pillar');

$dm->persist($customer);
$dm->flush();

$order = new \Graze\Dal\Dev\Order(4.99, $customer);
$order->addProduct($toy);

$dm->persist($order);
$dm->flush();

foreach ($customer->getOrders() as $order) {
    dump($order->getId());
    dump($order->getCustomer()->getFirstName() . ' ' . $order->getCustomer()->getLastName());

    foreach ($order->getProducts() as $product) {
        dump($product->getName());
    }
}
