<?php

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Adapter\Orm\EloquentOrmAdapter;
use Graze\Dal\DalManager;

class DalManagerTest extends \Graze\Dal\Test\FunctionalTestCase
{
    /**
     * @var ExtendedPdo
     */
    protected $pdo;

    public function tearDown()
    {
        parent::tearDown();

        $this->pdo->query('DROP TABLE IF EXISTS customer');
        $this->pdo->query('DROP TABLE IF EXISTS product');
        $this->pdo->query('DROP TABLE IF EXISTS `order`');
        $this->pdo->query('DROP TABLE IF EXISTS order_item');
    }

    public function setUp()
    {
        parent::setUp();
        $this->pdo = new ExtendedPdo('mysql:host=db;dbname=dal', 'root', 'password');

        $this->setUpDatabase();
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

    public function testFlushInLoop()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => 'dal_db',
            'port' => 3306,
            'username' => 'root',
            'password' => 'password',
            'database' => 'dal',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ], 'default');
        $capsule->bootEloquent();

        $dal = new DalManager([
            EloquentOrmAdapter::createFromYaml(
                $capsule->getConnection('default'),
                [__DIR__ . '/../config/eloquent.yml']
            )
        ]);

        foreach (['a', 'b'] as $n) {
            foreach ([1, 2, 3, 4, 5] as $i) {
                $product = new \Graze\Dal\Test\Entity\Product($n . $i, 1.00);

                $dal->persist($product);
                $dal->flush();
            }
        }
        $repository = $dal->getRepository('Graze\Dal\Test\Entity\Product');
        static::assertCount(10, $repository->findAll());
    }

    public function testFlushInOuterLoop()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => 'dal_db',
            'port' => 3306,
            'username' => 'root',
            'password' => 'password',
            'database' => 'dal',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ], 'default');
        $capsule->bootEloquent();

        $dal = new DalManager([
            EloquentOrmAdapter::createFromYaml(
                $capsule->getConnection('default'),
                [__DIR__ . '/../config/eloquent.yml']
            )
        ]);

        foreach (['a', 'b'] as $n) {
            foreach ([1, 2, 3, 4, 5] as $i) {
                $product = new \Graze\Dal\Test\Entity\Product($n . $i, 1.00);

                $dal->persist($product);
            }
            $dal->flush();
        }
        $repository = $dal->getRepository('Graze\Dal\Test\Entity\Product');
        static::assertCount(10, $repository->findAll());
    }

    public function testFlushInOutsideLoop()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => 'dal_db',
            'port' => 3306,
            'username' => 'root',
            'password' => 'password',
            'database' => 'dal',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci'
        ], 'default');
        $capsule->bootEloquent();

        $dal = new DalManager([
            EloquentOrmAdapter::createFromYaml(
                $capsule->getConnection('default'),
                [__DIR__ . '/../config/eloquent.yml']
            )
        ]);

        foreach (['a', 'b'] as $n) {
            foreach ([1, 2, 3, 4, 5] as $i) {
                $product = new \Graze\Dal\Test\Entity\Product($n . $i, 1.00);

                $dal->persist($product);
            }
        }
        $dal->flush();
        $repository = $dal->getRepository('Graze\Dal\Test\Entity\Product');
        static::assertCount(10, $repository->findAll());
    }
}
