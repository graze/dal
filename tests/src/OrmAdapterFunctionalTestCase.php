<?php

namespace Graze\Dal\Test;

use Aura\Sql\ExtendedPdo;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Test\Entity\Customer;
use Graze\Dal\Test\Entity\Order;
use Graze\Dal\Test\Entity\Product;

abstract class OrmAdapterFunctionalTestCase extends FunctionalTestCase
{
    /**
     * @var ExtendedPdo
     */
    protected $pdo;

    public function setUp()
    {
        parent::setUp();
        $this->pdo = new ExtendedPdo('mysql:host=db;dbname=dal', 'root', 'password');

        $this->setUpDatabase();
    }

    /**
     * @return DalManagerInterface[]
     */
    abstract public function buildDalManagers();

    /**
     * @param DalManagerInterface $dm
     * @dataProvider buildDalManagers
     */
    public function testSimpleManyToMany(DalManagerInterface $dm)
    {
        $toy = new Product('Toy', 1.99);
        $dm->persist($toy);

        $customer = new Customer('Will', 'Pillar');
        $dm->persist($customer);

        $dm->flush();

        $order = new Order($customer);
        $order->addProduct($toy);
        $dm->persist($order);

        $dm->flush();

        $orders = $customer->getOrders()->toArray();
        $order = $customer->getOrders()->first();

        static::assertCount(1, $orders);
        static::assertInstanceOf('Graze\Dal\Test\Entity\Order', $order);
        static::assertEquals($order->getCustomer()->getId(), $customer->getId());
    }

    /**
     * @param DalManagerInterface $dm
     * @dataProvider buildDalManagers
     */
    public function testComplexManyToMany(DalManagerInterface $dm)
    {
        $game = new Product('Game', 10.99);
        $dm->persist($game);

        $playstation = new Product('Playstation', 300);
        $dm->persist($playstation);

        $customerA = new Customer('Will', 'Pillar');
        $dm->persist($customerA);

        $customerB = new Customer('Jake', 'Montane');
        $dm->persist($customerB);

        $dm->flush();

        $order1 = new Order($customerA);
        $order1->addProduct($game);
        $order1->addProduct($playstation);
        $dm->persist($order1);

        $order2 = new Order($customerB);
        $order2->addProduct($playstation);
        $dm->persist($order2);

        $order3 = new Order($customerB);
        $order3->addProduct($game);
        $dm->persist($order3);

        $dm->flush();

        $customerAOrders = $customerA->getOrders()->toArray();
        $customerBOrders = $customerB->getOrders()->toArray();

        static::assertCount(1, $customerAOrders);
        static::assertCount(2, $customerBOrders);

        $customerAOrder = $customerA->getOrders()->first();
        $customerAOrderProducts = $customerAOrder->getProducts()->toArray();

        static::assertCount(2, $customerAOrderProducts);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->pdo->query('DROP TABLE IF EXISTS customer');
        $this->pdo->query('DROP TABLE IF EXISTS product');
        $this->pdo->query('DROP TABLE IF EXISTS `order`');
        $this->pdo->query('DROP TABLE IF EXISTS order_item');
    }

    private function setUpDatabase()
    {
        $this->pdo->query('DROP TABLE IF EXISTS customer');
        $this->pdo->query('DROP TABLE IF EXISTS product');
        $this->pdo->query('DROP TABLE IF EXISTS `order`');
        $this->pdo->query('DROP TABLE IF EXISTS order_item');

        foreach (glob(__DIR__ . '/../sql/*.sql') as $sqlFile) {
            $sql = file_get_contents($sqlFile);
            $this->pdo->query($sql);
        }
    }
}
