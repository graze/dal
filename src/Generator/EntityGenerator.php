<?php

namespace Graze\Dal\Generator;

use Doctrine\Common\Util\Inflector;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

class EntityGenerator extends AbstractClassGenerator implements GeneratorInterface
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
     * @return array
     */
    protected function buildClassGenerators()
    {
        $entities = [];

        foreach ($this->config as $name => $config) {
            if (array_key_exists('generate', $config) && ! $config['generate']) {
                continue;
            }
            $entity = $this->getClassGenerator($name);
            $entity->setImplementedInterfaces(['\\Graze\Dal\Entity\EntityInterface']);

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
                $interfaceGenerator = $this->buildInterfaceGeneratorFromClassGenerator($entity);
                $entities[$interfaceGenerator->getNamespaceName() . '\\' . $interfaceGenerator->getName()] = $interfaceGenerator;
            }

            $entities[$name] = $entity;
        }

        return $entities;
    }
}
