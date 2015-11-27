<?php

require __DIR__ . '/../vendor/autoload.php';

$dm = new \Graze\Dal\DalManager();
$pdo = new \Aura\Sql\ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');

$dm->set('pdo', new \Graze\Dal\Adapter\Pdo\PdoAdapter(
    new \Graze\Dal\Adapter\Pdo\Configuration\Configuration(
        $dm,
        $pdo,
        [
            'Graze\Dal\Dev\Customer' => [
                'table' => 'customer',
                'fields' => [
                    'id' => [
                        'mapsTo' => 'id'
                    ],
                    'firstName' => [
                        'mapsTo' => 'firstName'
                    ],
                    'lastName' => [
                        'mapsTo' => 'lastName'
                    ]
                ],
                'related' => [
                    'orders' => [
                        'type' => 'oneToMany',
                        'entity' => 'Graze\Dal\Dev\Order',
                        'foreignKey' => 'customer_id',
                        'collection' => true
                    ]
                ]
            ]
        ]
    )
));

$customer = new \Graze\Dal\Dev\Customer();
$customer->setFirstName('Will');
$customer->setLastName('Pillar');

$dm->persist($customer);
$dm->flush();
