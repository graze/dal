<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Graze\Dal\Adapter\DoctrineOrmAdapter;

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

$dm = new \Graze\Dal\DalManager(['main' => new DoctrineOrmAdapter($em)]);

$customer = new \Graze\Dal\Dev\Customer();
$customer->setFirstName('Will');
$customer->setLastName('Pillar');

$dm->persist($customer);
$dm->flush();

$customers = $dm->getRepository('Graze\Dal\Dev\Customer')->findAll();

dump($customers);
