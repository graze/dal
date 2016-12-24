<?php

namespace Graze\Dal\Generator;

class RepositoryGenerator extends AbstractClassGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $generateInterfaces;

    /**
     * @param array $config
     * @param bool $generateInterfaces
     */
    public function __construct(array $config, $generateInterfaces = false)
    {
        $this->config = $config;
        $this->generateInterfaces = $generateInterfaces;
    }

    /**
     * @return array
     */
    protected function buildClassGenerators()
    {
        $repositories = [];

        foreach ($this->config as $config) {
            if (! array_key_exists('repository', $config)) {
                continue;
            }

            $repository = $this->getClassGenerator($config['repository']);
            $repository->setExtendedClass('\\Graze\Dal\Repository\EntityRepository');

            if ($this->generateInterfaces) {
                $interfaceGenerator = $this->buildInterfaceGeneratorFromClassGenerator($repository);
                $repositories[$interfaceGenerator->getName()] = $interfaceGenerator;
            }

            $repositories[$config['repository']] = $repository;
        }

        return $repositories;
    }
}
