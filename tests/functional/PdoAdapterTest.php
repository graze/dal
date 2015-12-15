<?php

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Adapter\Orm\OrmAdapterInterface;
use Graze\Dal\Adapter\Pdo\PdoAdapter;
use Graze\Dal\DalManager;
use Graze\Dal\DalManagerInterface;
use Graze\Dal\Test\OrmAdapterFunctionalTestCase;

class PdoAdapterTest extends OrmAdapterFunctionalTestCase
{
    /**
     * @return OrmAdapterInterface[]
     */
    protected function buildAdapters()
    {
        $pdo = new ExtendedPdo('mysql:host=localhost;dbname=dal', 'root', 'password');
        return [
            PdoAdapter::createFromYaml($pdo, [__DIR__ . '/../config/pdo.yml'])
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
