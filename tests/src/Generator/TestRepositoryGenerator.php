<?php

namespace Graze\Dal\Test\Generator;

use Graze\Dal\Generator\RepositoryGenerator;
use Zend\Code\Generator\InterfaceGenerator;

class TestRepositoryGenerator extends RepositoryGenerator
{
    /**
     * @return array
     */
    public function getClassGenerators()
    {
        return $this->buildClassGenerators();
    }

    /**
     * @return array
     */
    public function getInterfaceGenerators()
    {
        $classGenerators = $this->getClassGenerators();
        $interfaceGenerators = [];

        foreach ($classGenerators as $classGenerator) {
            if ($classGenerator instanceof InterfaceGenerator) {
                $interfaceGenerators[] = $classGenerator;
            }
        }

        return $interfaceGenerators;
    }
}
