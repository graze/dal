<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Graze\Dal\Adapter\DoctrineOrm\Configuration;
use Graze\Dal\Adapter\DoctrineOrmAdapter;
use Graze\Dal\Adapter\EloquentOrm\Configuration as EloquentConfiguration;
use Graze\Dal\Adapter\EloquentOrmAdapter;
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

$dm->set('doctrine', new DoctrineOrmAdapter(new Configuration(
	$dm,
	[
		'Graze\Dal\Dev\Customer' => [
			'record' => 'Graze\Dal\Dev\DoctrineOrm\Customer',
			'related' => [
				'orders' => [
					'type' => 'manyToOne',
					'entity' => 'Graze\Dal\Dev\Order',
					'foreignKey' => 'customer_id'
				]
			]
		]
	],
	$em
), $em));

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

$dm->set('eloquent', new EloquentOrmAdapter($capsule->getConnection('default'), new EloquentConfiguration(
	$dm,
	[
		'Graze\Dal\Dev\Order' => [
			'record' => 'Graze\Dal\Dev\EloquentOrm\Order',
			'related' => [
				'customer' => [
					'type' => 'oneToMany',
					'entity' => 'Graze\Dal\Dev\Customer',
					'localKey' => 'customer_id'
				]
			]
		]
	]
)));

$customer = new \Graze\Dal\Dev\Customer();
$customer->setFirstName('Will');
$customer->setLastName('Pillar');

$dm->persist($customer);
$dm->flush();

$order = new \Graze\Dal\Dev\Order();
$order->setCustomer($customer);
$order->setPrice(5.99);

$dm->persist($order);
$dm->flush();

$customers = $dm->getRepository('Graze\Dal\Dev\Customer')->findAll();

dump($customers);
