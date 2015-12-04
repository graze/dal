<?php

use Graze\Dal\Adapter\Orm\OrmAdapterInterface;
use Graze\Dal\Adapter\Pdo\PdoAdapter;

class PdoAdapterTest extends \Graze\Dal\Test\OrmAdapterFunctionalTestCase
{
    /**
     * @return OrmAdapterInterface[]
     */
    protected function buildAdapters()
    {
        return [
            PdoAdapter::factory($this->pdo, __DIR__ . '/../config/pdo.yml')
        ];
    }
}
