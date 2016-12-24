<?php

namespace Graze\Dal\Generator;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\InterfaceGenerator;

class RepositoryGenerator implements GeneratorInterface
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
     * @return mixed
     */
    public function generate()
    {
        $repositories = [];

        foreach ($this->config as $config) {
            if (! array_key_exists('repository', $config)) {
                continue;
            }

            $repository = new ClassGenerator();
            $repository->setName($config['repository']);
            $repository->setExtendedClass('\\Graze\Dal\Repository\EntityRepository');

            if ($this->generateInterfaces) {
                $interfaceName = $config['repository'] . 'Interface';
                $repositoryInterface = new InterfaceGenerator();
                $repositoryInterface->setName($interfaceName);

                $file = FileGenerator::fromArray(['classes' => [$repositoryInterface]]);
                $repositories[$interfaceName] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";

                $repository->setImplementedInterfaces(['\\' . $interfaceName]);
            }

            $file = FileGenerator::fromArray(['classes' => [$repository]]);
            $repositories[$config['repository']] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";
        }

        return $repositories;
    }
}
