<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Graze\Dal\Adapter\Orm\DoctrineOrmAdapter;
use Graze\Dal\Adapter\Orm\EloquentOrmAdapter;
use Graze\Dal\DalManager;

require __DIR__ . '/../vendor/autoload.php';

$config = Setup::createYAMLMetadataConfiguration(array(__DIR__.'/config/'), true);

$conn = [
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'port' => 3306,
    'user' => 'root',
    'password' => 'password',
    'dbname' => 'dal',
    'charset'   => 'utf8',
    'serverVersion' => 5.6,
];

$em = EntityManager::create($conn, $config);
$dm = new DalManager();

$dm->set('doctrine', DoctrineOrmAdapter::factory($em, __DIR__ . '/config/doctrine.yml'));

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'username' => 'root',
    'password' => 'password',
    'database' => 'dal',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci'
], 'default');
$capsule->bootEloquent();

$dm->set('eloquent', EloquentOrmAdapter::factory($capsule->getConnection('default'), __DIR__ . '/config/eloquent.yml'));

$em->getConnection()->executeQuery('TRUNCATE `order`');
$em->getConnection()->executeQuery('TRUNCATE `customer`');
$em->getConnection()->executeQuery('TRUNCATE `product`');
$em->getConnection()->executeQuery('TRUNCATE `order_item`');

$toy = new \Graze\Dal\Dev\Product('Toy', 1.99);
$game = new \Graze\Dal\Dev\Product('Game', 10.99);

$dm->persist($toy);
$dm->persist($game);
$dm->flush();

$customer = new \Graze\Dal\Dev\Customer('Will', 'Pillar');

$dm->persist($customer);
$dm->flush();

$order = new \Graze\Dal\Dev\Order(5.99, $customer);
$order->addProduct($toy);

$dm->persist($order);

$order = new \Graze\Dal\Dev\Order(10.00, $customer);
$order->addProduct($toy);
$order->addProduct($game);

$dm->persist($order);
$dm->flush();

//$customer = $dm->getRepository('Graze\Dal\Dev\Customer')->find(1);
//
//dump($customer);

foreach ($customer->getOrders() as $order) {
    dump($order->getId());
    dump($order->getCustomer()->getFirstName() . ' ' . $order->getCustomer()->getLastName());

    foreach ($order->getProducts() as $product) {
        dump($product->getName());
    }
}

//$customers = $dm->getRepository('Graze\Dal\Dev\Customer')->findAll();
//dump($customers);

