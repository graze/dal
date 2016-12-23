<?php

namespace Graze\Dal\Generator;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;

class RepositoryGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
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

            $file = FileGenerator::fromArray(['classes' => [$repository]]);
            $repositories[$config['repository']] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";
        }

        return $repositories;
    }
}
