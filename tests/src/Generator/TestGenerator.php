<?php

namespace Graze\Dal\Test\Generator;

use Graze\Dal\Generator\AbstractClassGenerator;
use Zend\Code\Generator\ClassGenerator;

class TestGenerator extends AbstractClassGenerator
{
    /**
     * @var callable
     */
    private $buildClassGeneratorsCallback;

    /**
     * @return array
     */
    protected function buildClassGenerators()
    {
        $callable = $this->buildClassGeneratorsCallback;
        return $callable();
    }

    /**
     * @param callable $callback
     */
    public function buildClassGeneratorsUsing(callable $callback)
    {
        $this->buildClassGeneratorsCallback = $callback;
    }

    /**
     * @return array
     */
    public function getClassGenerators()
    {
        return $this->buildClassGenerators();
    }

    /**
     * @param \Zend\Code\Generator\ClassGenerator $classGenerator
     *
     * @return \Zend\Code\Generator\InterfaceGenerator
     */
    public function buildInterfaceGenerator(ClassGenerator $classGenerator)
    {
        return $this->buildInterfaceGeneratorFromClassGenerator($classGenerator);
    }
}
