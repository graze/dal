<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Generator;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;

abstract class AbstractClassGenerator
{
    /**
     * @return array
     */
    abstract protected function buildClassGenerators();

    /**
     * @return array
     */
    public function generate()
    {
        $classGenerators = $this->buildClassGenerators();
        return $this->generateClassStrings($classGenerators);
    }

    /**
     * @param string $className
     *
     * @return \Zend\Code\Generator\ClassGenerator
     */
    protected function getClassGenerator($className)
    {
        $generator = new ClassGenerator();
        $generator->setName($className);
        return $generator;
    }

    /**
     * @param string $interfaceName
     *
     * @return \Zend\Code\Generator\InterfaceGenerator
     */
    protected function getInterfaceGenerator($interfaceName)
    {
        $generator = new InterfaceGenerator();
        $generator->setName($interfaceName);
        return $generator;
    }

    /**
     * @param \Zend\Code\Generator\ClassGenerator $classGenerator
     *
     * @return \Zend\Code\Generator\InterfaceGenerator
     */
    protected function buildInterfaceGeneratorFromClassGenerator(ClassGenerator $classGenerator)
    {
        $interfaceName = $classGenerator->getNamespaceName() . '\\' . $classGenerator->getName(). 'Interface';
        $interfaceGenerator = $this->getInterfaceGenerator($interfaceName);

        foreach ($classGenerator->getMethods() as $method) {
            $interfaceGenerator->addMethodFromGenerator(clone $method);
        }

        $implementedInterfaces = $classGenerator->getImplementedInterfaces();
        $classGenerator->setImplementedInterfaces(array_merge($implementedInterfaces, ['\\' . $interfaceName]));

        return $interfaceGenerator;
    }

    /**
     * @param ClassGenerator $entity
     * @param string $property
     * @param string $type
     * @param string $cast
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    protected function addGetter(ClassGenerator $entity, $property, $type, $cast = ' ')
    {
        $methodName = 'get' . ucfirst($property);
        $entity->addMethod(
            $methodName,
            [],
            MethodGenerator::FLAG_PUBLIC,
            'return' . $cast . '$this->' . $property . ';',
            DocBlockGenerator::fromArray([
                'tags' => [
                    new ReturnTag(['datatype' => $type])
                ]
            ])
        );
    }

    /**
     * @param ClassGenerator $entity
     * @param string $property
     * @param string $type
     * @param string $cast
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    protected function addSetter(ClassGenerator $entity, $property, $type, $cast = ' ')
    {
        $methodName = 'set' . ucfirst($property);
        $entity->addMethod(
            $methodName,
            [['name' => $property, 'type' => $type]],
            MethodGenerator::FLAG_PUBLIC,
            '$this->' . $property . ' =' . $cast . '$' . $property . ';',
            DocBlockGenerator::fromArray([
                'tags' => [
                    new ParamTag($property, $type)
                ]
            ])
        );
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    protected function generateClassStrings(array $classes)
    {
        $classStrings = [];

        /** @var ClassGenerator $class */
        foreach ($classes as $name => $class) {
            $classStrings[$name] = $this->generateClassString($class);
        }

        return $classStrings;
    }

    /**
     * @param \Zend\Code\Generator\ClassGenerator $class
     *
     * @return string
     */
    protected function generateClassString(ClassGenerator $class)
    {
        $file = FileGenerator::fromArray(['classes' => [$class]]);
        return rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";
    }
}
