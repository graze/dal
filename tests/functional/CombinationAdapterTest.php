<?php

use Aura\Sql\ExtendedPdo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class CombinationAdapterTest extends \Graze\Dal\Test\OrmAdapterFunctionalTestCase
{
    /**
     * @return \Graze\Dal\DalManagerInterface[]
     */
    public function buildDalManagers()
    {
        $matrix = $this->buildMatrix();
        $managers = [];

        foreach ($matrix as $combo) {
            $dm = new \Graze\Dal\DalManager();

            foreach ($combo as $entity => $adapterName) {
                $dm->set(uniqid(), $this->buildAdapter($entity, $adapterName));
            }

            $managers[] = $dm;
        }

        return [$managers];
    }

    private function buildAdapter($entity, $name)
    {
        switch ($name) {
            case 'PDO':
                $pdo = new ExtendedPdo('mysql:host=db;dbname=dal', 'root', 'password');
                $config = new \Graze\Dal\Adapter\Pdo\Configuration\Configuration($pdo, $this->buildConfig($entity, $name));
                return new \Graze\Dal\Adapter\Pdo\PdoAdapter($pdo, $config);
            case 'Doctrine':
                $config = Setup::createYAMLMetadataConfiguration(array(__DIR__.'/../config/'), true);
                $conn = [
                    'driver' => 'pdo_mysql',
                    'host' => 'db',
                    'port' => 3306,
                    'user' => 'root',
                    'password' => 'password',
                    'dbname' => 'dal',
                    'charset'   => 'utf8',
                    'serverVersion' => 5.6,
                ];
                $em = EntityManager::create($conn, $config);
                return new \Graze\Dal\Adapter\Orm\DoctrineOrmAdapter($em, new \Graze\Dal\Adapter\Orm\DoctrineOrm\Configuration($em, $this->buildConfig($entity, $name)));
            case 'Eloquent':
                $capsule = new \Illuminate\Database\Capsule\Manager();
                $capsule->addConnection([
                    'driver' => 'mysql',
                    'host' => 'db',
                    'port' => 3306,
                    'username' => 'root',
                    'password' => 'password',
                    'database' => 'dal',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci'
                ], 'default');
                $capsule->bootEloquent();
                return new \Graze\Dal\Adapter\Orm\EloquentOrmAdapter($capsule->getConnection('default'), new \Graze\Dal\Adapter\Orm\EloquentOrm\Configuration($this->buildConfig($entity, $name)));
        }
    }

    private function buildConfig($entity, $name)
    {
        switch ($name) {
            case 'PDO':
                $config = [
                    'Graze\Dal\Test\Entity\Customer' => [
                        'table' => 'customer',
                        'related' => [
                            'orders' => [
                                'type' => 'oneToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Order',
                                'foreignKey' => 'customer_id',
                                'collection' => true
                            ]
                        ],
                        'fields' => [
                            'firstName' => [
                                'mapsTo' => 'first_name'
                            ],
                            'lastName' => [
                                'mapsTo' => 'last_name'
                            ],
                        ]
                    ],
                    'Graze\Dal\Test\Entity\Product' => [
                        'table' => 'product'
                    ],
                    'Graze\Dal\Test\Entity\Order' => [
                        'table' => 'order',
                        'related' => [
                            'customer' => [
                                'type' => 'manyToOne',
                                'entity' => 'Graze\Dal\Test\Entity\Customer',
                                'localKey' => 'customer_id'
                            ],
                            'products' => [
                                'type' => 'manyToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Product',
                                'pivot' => 'order_item',
                                'localKey' => 'order_id',
                                'foreignKey' => 'product_id',
                                'collection' => true
                            ]
                        ]
                    ]
                ];
                return [$entity => $config[$entity]];
            case 'Doctrine':
                $config = [
                    'Graze\Dal\Test\Entity\Customer' => [
                        'record' => 'Graze\Dal\Test\DoctrineOrm\Customer',
                        'related' => [
                            'orders' => [
                                'type' => 'oneToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Order',
                                'foreignKey' => 'customer_id',
                                'collection' => true
                            ]
                        ],
                        'fields' => [
                            'firstName' => [
                                'mapsTo' => 'first_name'
                            ],
                            'lastName' => [
                                'mapsTo' => 'last_name'
                            ],
                        ]
                    ],
                    'Graze\Dal\Test\Entity\Product' => [
                        'record' => 'Graze\Dal\Test\DoctrineOrm\Product'
                    ],
                    'Graze\Dal\Test\Entity\Order' => [
                        'record' => 'Graze\Dal\Test\DoctrineOrm\Order',
                        'related' => [
                            'customer' => [
                                'type' => 'manyToOne',
                                'entity' => 'Graze\Dal\Test\Entity\Customer',
                                'localKey' => 'customer_id'
                            ],
                            'products' => [
                                'type' => 'manyToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Product',
                                'pivot' => 'order_item',
                                'localKey' => 'order_id',
                                'foreignKey' => 'product_id',
                                'collection' => true
                            ]
                        ]
                    ]
                ];
                return [$entity => $config[$entity]];
            case 'Eloquent':
                $config = [
                    'Graze\Dal\Test\Entity\Customer' => [
                        'record' => 'Graze\Dal\Test\EloquentOrm\Customer',
                        'related' => [
                            'orders' => [
                                'type' => 'oneToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Order',
                                'foreignKey' => 'customer_id',
                                'collection' => true
                            ]
                        ],
                        'fields' => [
                            'firstName' => [
                                'mapsTo' => 'first_name'
                            ],
                            'lastName' => [
                                'mapsTo' => 'last_name'
                            ],
                        ]
                    ],
                    'Graze\Dal\Test\Entity\Product' => [
                        'record' => 'Graze\Dal\Test\EloquentOrm\Product'
                    ],
                    'Graze\Dal\Test\Entity\Order' => [
                        'record' => 'Graze\Dal\Test\EloquentOrm\Order',
                        'related' => [
                            'customer' => [
                                'type' => 'manyToOne',
                                'entity' => 'Graze\Dal\Test\Entity\Customer',
                                'localKey' => 'customer_id'
                            ],
                            'products' => [
                                'type' => 'manyToMany',
                                'entity' => 'Graze\Dal\Test\Entity\Product',
                                'pivot' => 'order_item',
                                'localKey' => 'order_id',
                                'foreignKey' => 'product_id',
                                'collection' => true
                            ]
                        ]
                    ]
                ];
                return [$entity => $config[$entity]];
        }
    }

    private function buildMatrix()
    {
        $combinations = [
            'Graze\Dal\Test\Entity\Customer' => ['PDO', 'Doctrine', 'Eloquent'],
            'Graze\Dal\Test\Entity\Product' => ['PDO', 'Doctrine', 'Eloquent'],
            'Graze\Dal\Test\Entity\Order' => ['PDO', 'Doctrine', 'Eloquent']
        ];

        $matrix = [];
        $counters = [
            'Graze\Dal\Test\Entity\Customer' => 0,
            'Graze\Dal\Test\Entity\Product' => 0,
            'Graze\Dal\Test\Entity\Order' => 0,
        ];
        while (count($matrix) < 27) {
            $combo = [];
            foreach ($combinations as $entity => $adapters) {
                $combo[$entity] = $adapters[$counters[$entity]];
            }

            $hash = md5(json_encode($combo));

            if (!array_key_exists($hash, $matrix)) {
                // reset back to 0,0,0
                foreach ($counters as $entity => $counter) {
                    $counters[$entity] = mt_rand(0, 2);
                }
                $matrix[$hash] = $combo;
            }
            if ($counters['Graze\Dal\Test\Entity\Customer'] < 2) {
                $counters['Graze\Dal\Test\Entity\Customer']++;
            } elseif ($counters['Graze\Dal\Test\Entity\Order'] < 2) {
                $counters['Graze\Dal\Test\Entity\Order']++;
            } elseif ($counters['Graze\Dal\Test\Entity\Product'] < 2) {
                $counters['Graze\Dal\Test\Entity\Product']++;
            } else {
                foreach ($counters as $entity => $counter) {
                    $counters[$entity] = mt_rand(0, 2);
                }
            }
        }

        return $matrix;
    }
}
