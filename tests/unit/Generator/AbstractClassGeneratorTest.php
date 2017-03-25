<?php

namespace Graze\Dal\Test\Unit\Generator;

use Graze\Dal\Test\Generator\TestGenerator;
use Zend\Code\Generator\ClassGenerator;

class AbstractClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reveals https://github.com/graze/dal/issues/35
     */
    public function testInterfaceGeneratedWithoutConstructor()
    {
        $testGenerator = new TestGenerator();
        $testGenerator->buildClassGeneratorsUsing(function () {
            $classGenerator = new ClassGenerator();
            $classGenerator->addMethod('__construct');

            return [$classGenerator];
        });

        $classGenerators = $testGenerator->getClassGenerators();
        $interfaceGenerator = $testGenerator->buildInterfaceGenerator($classGenerators[0]);

        static::assertFalse($interfaceGenerator->hasMethod('__construct'));
    }
}
