<?php

namespace Graze\Dal\Generator;

use Doctrine\Common\Util\Inflector;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

class EntityGenerator
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $getters;

    /**
     * @var bool
     */
    private $setters;

    /**
     * @var bool
     */
    private $interfaces;

    /**
     * @param array $config
     * @param bool $getters
     * @param bool $setters
     * @param bool $interfaces
     */
    public function __construct(array $config, $getters = true, $setters = true, $interfaces = false)
    {
        $this->config = $config;
        $this->getters = $getters;
        $this->setters = $setters;
        $this->interfaces = $interfaces;
    }

    /**
     * @return array
     */
    public function generate()
    {
        $entities = [];

        foreach ($this->config as $name => $config) {
            if (array_key_exists('generate', $config) && ! $config['generate']) {
                continue;
            }
            $entity = $this->getClassGenerator($name);

            $this->addConstructor($entity, $config);

            if (array_key_exists('fields', $config)) {
                foreach ($config['fields'] as $property => $fieldConfig) {
                    $cast = ' ';
                    $type = 'string';

                    if (array_key_exists('type', $fieldConfig)) {
                        $type = $fieldConfig['type'];
                        $cast = ' ('. $type . ') ';
                    }

                    $this->addProperty($entity, $property);

                    if ($this->setters && $property !== 'id') {
                        $readonly = array_key_exists('readonly', $fieldConfig) ? $fieldConfig['readonly'] : false;
                        if (! $readonly) {
                            $this->addSetter($entity, $property, $type, $cast);
                        }
                    }

                    if ($this->getters) {
                        $this->addGetter($entity, $property, $type, $cast);
                    }
                }
            }

            if (array_key_exists('related', $config)) {
                foreach ($config['related'] as $relation => $relationConfig) {
                    $this->addProperty($entity, $relation);
                    $type = $this->getRelationType($relationConfig);

                    if ($this->getters) {
                        $this->addGetter($entity, $relation, $type);
                    }

                    if ($this->setters) {
                        $this->addSetter($entity, $relation, $type);
                    }

                    if (array_key_exists('collection', $relationConfig) && $relationConfig['collection']) {
                        $type = '\\' . $relationConfig['entity'];
                        $addMethodName = 'add' . ucfirst(Inflector::singularize($relation));
                        $entity->addMethod(
                            $addMethodName,
                            [['name' => $relation, 'type' => $type]],
                            MethodGenerator::FLAG_PUBLIC,
                            '$this->' . $relation . '->add($' . $relation . ');',
                            DocBlockGenerator::fromArray([
                                'tags' => [
                                    new ParamTag($relation, $type)
                                ]
                            ])
                        );
                    }
                }
            }

            if ($this->interfaces) {
                $interfaceName = $name . 'Interface';
                $entityInterface = new InterfaceGenerator();
                $entityInterface->setName($interfaceName);

                foreach ($entity->getMethods() as $method) {
                    $entityInterface->addMethodFromGenerator(clone $method);
                }

                $file = FileGenerator::fromArray(['classes' => [$entityInterface]]);
                $entities[$interfaceName] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";

                $entity->setImplementedInterfaces(['\\' . $interfaceName]);
            }

            $file = FileGenerator::fromArray(['classes' => [$entity]]);
            $entities[$name] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";
        }

        return $entities;
    }

    /**
     * @param ClassGenerator $entity
     * @param string $property
     * @param string $type
     * @param string $cast
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    private function addGetter(ClassGenerator $entity, $property, $type, $cast = ' ')
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
    private function addSetter(ClassGenerator $entity, $property, $type, $cast = ' ')
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
     * @param ClassGenerator $entity
     * @param string $property
     * @param mixed $default
     * @param int $visibility
     */
    private function addProperty(ClassGenerator $entity, $property, $default = null, $visibility = PropertyGenerator::FLAG_PRIVATE)
    {
        $entity->addProperty($property, $default, $visibility);
    }

    /**
     * @param ClassGenerator $entity
     * @param array $config
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    private function addConstructor(ClassGenerator $entity, array $config)
    {
        $entity->addMethod(
            '__construct',
            $this->buildConstructorParameters($config),
            MethodGenerator::FLAG_PUBLIC,
            $this->buildConstructorBody($config),
            DocBlockGenerator::fromArray($this->buildConstructorDocBlock($config))
        );
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function buildConstructorBody(array $config)
    {
        $body = '';

        if (array_key_exists('fields', $config)) {
            foreach ($config['fields'] as $name => $fieldConfig) {
                if ($name === 'id') {
                    continue;
                }
                $cast = ' ';
                if (array_key_exists('type', $fieldConfig)) {
                    $type = $fieldConfig['type'];
                    $cast = ' ('. $type . ') ';
                }
                $body .= '$this->' . $name . ' =' . $cast . '$' . $name . ';' . PHP_EOL;
            }
        }

        if (array_key_exists('related', $config)) {
            foreach ($config['related'] as $name => $relationConfig) {
                if (array_key_exists('collection', $relationConfig) && $relationConfig['collection']) {
                    $type = $this->getRelationType($relationConfig);
                    $body .= '$this->' . $name . ' = new ' . $type . '();' . PHP_EOL;
                } else {
                    $body .= '$this->' . $name . ' = $' . $name . ';' . PHP_EOL;
                }
            }
        }

        return $body;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function buildConstructorDocBlock(array $config)
    {
        $docBlock = ['tags' => []];
        $type = 'string';

        if (array_key_exists('fields', $config)) {
            foreach ($config['fields'] as $name => $fieldConfig) {
                if ($name === 'id') {
                    continue;
                }
                if (array_key_exists('type', $fieldConfig)) {
                    $type = $fieldConfig['type'];
                }
                $docBlock['tags'][] = new ParamTag($name, $type);
            }
        }

        if (array_key_exists('related', $config)) {
            foreach ($config['related'] as $name => $relationConfig) {
                if (array_key_exists('collection', $relationConfig) && $relationConfig['collection']) {
                    continue;
                }
                $docBlock['tags'][] = new ParamTag($name, $this->getRelationType($relationConfig));
            }
        }

        return $docBlock;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function buildConstructorParameters(array $config)
    {
        $params = [];

        if (array_key_exists('fields', $config)) {
            foreach ($config['fields'] as $name => $fieldConfig) {
                if ($name === 'id') {
                    continue;
                }
                $params[] = $name;
            }
        }

        if (array_key_exists('related', $config)) {
            foreach ($config['related'] as $name => $relationConfig) {
                if (array_key_exists('collection', $relationConfig) && $relationConfig['collection']) {
                    continue;
                }
                $params[] = ['name' => $name, 'type' => $this->getRelationType($relationConfig)];
            }
        }

        return $params;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function getRelationType(array $config)
    {
        $returnType = $config['entity'];

        if (array_key_exists('collection', $config) && $config['collection']) {
            $returnType = 'Doctrine\Common\Collections\ArrayCollection';
            if (is_string($config['collection'])) {
                $returnType = ltrim('\\', $config['collection']);
            }
        }

        return '\\' . $returnType;
    }

    /**
     * @param string $name
     *
     * @return ClassGenerator
     */
    private function getClassGenerator($name)
    {
        $generator = new ClassGenerator();
        $generator->setName($name);
        $generator->setImplementedInterfaces(['\\Graze\Dal\Entity\EntityInterface']);

        return $generator;
    }
}
