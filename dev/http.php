<?php

require __DIR__ . '/../vendor/autoload.php';

$dm = new \Graze\Dal\DalManager();
$pdo = new \Aura\Sql\ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');

$dm->set('pdo', new \Graze\Dal\Adapter\Pdo\PdoAdapter(
    $pdo,
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
            ]
        ]
    )
));

$json = '{"data":{"id":1,"name":"Toy","price":3.99}}';

$response = Mockery::mock('Psr\Http\Message\ResponseInterface');
$response->shouldReceive('getBody')
    ->andReturn($json);

$client = Mockery::mock('\GuzzleHttp\Client');
$client->shouldReceive('request')
    ->with('GET', 'https://product.graze.com:80/product/1', [])
    ->andReturn($response);

$dm->set('rest', new \Graze\Dal\Adapter\Http\Rest\RestAdapter(
    $client,
    new \Graze\Dal\Adapter\Http\Rest\Configuration\Configuration(
        $dm,
        $client,
        [
            'Graze\Dal\Dev\Product' => [
                'host' => 'https://product.graze.com',
                'port' => 80,
                'resource' => 'product',
                'options' => []
            ]
        ]
    )
));

$toy = $dm->getRepository('Graze\Dal\Dev\Product')->find(1);

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
