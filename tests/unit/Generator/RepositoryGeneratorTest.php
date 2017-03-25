<?php

namespace Graze\Dal\Test\Unit\Generator;

use Graze\Dal\Test\Generator\TestRepositoryGenerator;
use Zend\Code\Generator\InterfaceGenerator;

class RepositoryGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratedInterfaceExtendsObjectRepository()
    {
        $repositoryGenerator = new TestRepositoryGenerator([
            'Foo' => [
                'repository' => 'FooRepository'
            ]
        ], true);

        $interfaceGenerators = $repositoryGenerator->getInterfaceGenerators();

        /** @var InterfaceGenerator $interfaceGenerator */
        $interfaceGenerator = $interfaceGenerators[0];

        static::assertContains('\Doctrine\Common\Persistence\ObjectRepository', $interfaceGenerator->getImplementedInterfaces());
    }
}
