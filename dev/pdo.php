<?php

require __DIR__ . '/../vendor/autoload.php';

$dm = new \Graze\Dal\DalManager();
$pdo = new \Aura\Sql\ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');

$dm->set('pdo', new \Graze\Dal\Adapter\Pdo\PdoAdapter(
    $pdo,
    new \Graze\Dal\Adapter\Pdo\Configuration\Configuration(
        $pdo,
        [
            'Graze\Dal\Dev\Customer' => [
                'table' => 'customer',
                'fields' => [
                    'id' => [
                        'mapsTo' => 'id'
                    ],
                    'firstName' => [
                        'mapsTo' => 'first_name'
                    ],
                    'lastName' => [
                        'mapsTo' => 'last_name'
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
            ],
            'Graze\Dal\Dev\Order' => [
                'table' => 'order',
                'fields' => [
                    'id' => [
                        'mapsTo' => 'id'
                    ],
                    'price' => [
                        'mapsTo' => 'price'
                    ]
                ],
                'related' => [
                    'customer' => [
                        'type' => 'manyToOne',
                        'entity' => 'Graze\Dal\Dev\Customer',
                        'localKey' => 'customer_id'
                    ],
                    'products' => [
                        'type' => 'manyToMany',
                        'entity' => 'Graze\Dal\Dev\Product',
                        'pivot' => 'order_item',
                        'localKey' => 'order_id',
                        'foreignKey' => 'product_id',
                        'collection' => true
                    ]
                ]
            ],
            'Graze\Dal\Dev\Product' => [
                'table' => 'product'
            ]
        ]
    )
));

$toy = new \Graze\Dal\Dev\Product();
$toy->setName('Toy');
$toy->setPrice(1.99);

$dm->persist($toy);
$dm->flush();

$customer = new \Graze\Dal\Dev\Customer();
$customer->setFirstName('Will');
$customer->setLastName('Pillar');

$dm->persist($customer);
$dm->flush();

$order = new \Graze\Dal\Dev\Order();
$order->setCustomer($customer);
$order->setPrice(4.99);
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
