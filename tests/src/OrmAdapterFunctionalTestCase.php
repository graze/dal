<?php

namespace Graze\Dal\Test;

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Adapter\Orm\OrmAdapterInterface;
use Graze\Dal\DalManager;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Test\Entity\Customer;
use Graze\Dal\Test\Entity\Order;
use Graze\Dal\Test\Entity\Product;

abstract class OrmAdapterFunctionalTestCase extends FunctionalTestCase
{
    /**
     * @var DalManagerInterface
     */
    protected $dm;

    /**
     * @var ExtendedPdo
     */
    protected $pdo;

    public function setUp()
    {
        parent::setUp();
        $this->pdo = new ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');

        $this->setUpDatabase();

        $this->dm = new DalManager();

        foreach ($this->buildAdapters() as $adapter) {
            $this->dm->set(uniqid(), $adapter);
        }
    }

    /**
     * @return OrmAdapterInterface[]
     */
    abstract protected function buildAdapters();

    public function testSimpleManyToMany()
    {
        $toy = new Product('Toy', 1.99);
        $this->dm->persist($toy);

        $customer = new Customer('Will', 'Pillar');
        $this->dm->persist($customer);

        $this->dm->flush();

        $order = new Order($customer);
        $order->addProduct($toy);
        $this->dm->persist($order);

        $this->dm->flush();

        $orders = $customer->getOrders()->toArray();
        $order = $customer->getOrders()->first();

        static::assertCount(1, $orders);
        static::assertInstanceOf('Graze\Dal\Test\Entity\Order', $order);
        static::assertEquals($order->getCustomer()->getId(), $customer->getId());
    }

    public function testComplexManyToMany()
    {
        $game = new Product('Game', 10.99);
        $this->dm->persist($game);

        $playstation = new Product('Playstation', 300);
        $this->dm->persist($playstation);

        $customerA = new Customer('Will', 'Pillar');
        $this->dm->persist($customerA);

        $customerB = new Customer('Jake', 'Montane');
        $this->dm->persist($customerB);

        $this->dm->flush();

        $order1 = new Order($customerA);
        $order1->addProduct($game);
        $order1->addProduct($playstation);
        $this->dm->persist($order1);

        $order2 = new Order($customerB);
        $order2->addProduct($playstation);
        $this->dm->persist($order2);

        $order3 = new Order($customerB);
        $order3->addProduct($game);
        $this->dm->persist($order3);

        $this->dm->flush();

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
