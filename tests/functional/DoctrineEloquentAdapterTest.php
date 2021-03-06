<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Graze\Dal\Adapter\Orm\DoctrineOrmAdapter;
use Graze\Dal\Adapter\Orm\EloquentOrmAdapter;
use Graze\Dal\DalManager;
use Graze\Dal\DalManagerInterface;

class DoctrineEloquentAdapterTest extends \Graze\Dal\Test\OrmAdapterFunctionalTestCase
{
    /**
     * @return \Graze\Dal\Adapter\Orm\OrmAdapterInterface[]
     */
    protected function buildAdapters()
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

        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__.'/../config/'), true);

        $conn = [
            'driver' => 'pdo_mysql',
            'host' => 'dal_db',
            'port' => 3306,
            'user' => 'root',
            'password' => 'password',
            'dbname' => 'dal',
            'charset'   => 'utf8',
            'serverVersion' => 5.6,
        ];

        $em = EntityManager::create($conn, $config);

        return [
            EloquentOrmAdapter::createFromYaml($capsule->getConnection('default'), [__DIR__ . '/../config/eloquent.yml']),
            DoctrineOrmAdapter::createFromYaml($em, [__DIR__ . '/../config/doctrine.yml'])
        ];
    }

    /**
     * @return DalManagerInterface[]
     */
    public function buildDalManagers()
    {
        $dm = new DalManager();

        foreach ($this->buildAdapters() as $adapter) {
            $dm->set(uniqid(), $adapter);
        }

        return [[$dm]];
    }
}
