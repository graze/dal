<?php

namespace Graze\Dal\Console\Command;

use Graze\Dal\Adapter\GeneratableInterface;
use Graze\Dal\Generator\GeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class GenerateRecordsCommand extends Command
{
    use ClassPersisterTrait;

    protected function configure()
    {
        $this->setName('generate:records')
            ->setDescription('Use provided config to generate records.')
            ->addArgument('config', InputArgument::REQUIRED, 'The YAML config file to generate from.')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the entities (e.g. Acme\\\\Dal).')
            ->addArgument('directory', InputArgument::REQUIRED, 'The source directory for the generated entities');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $input->getArgument('config');
        $config = (new Parser())->parse(file_get_contents($configPath));

        $records = [];

        foreach ($config as $entityName => $entityConfig) {
            if (! array_key_exists('adapter', $entityConfig)) {
                throw new \InvalidArgumentException(
                    $entityName . " Missing config field 'adapter' required for record generation"
                );
            }

            /** @var GeneratableInterface $adapterName */
            $adapterName = $entityConfig['adapter'];
            $reflectionClass = new \ReflectionClass($adapterName);

            if (! $reflectionClass->implementsInterface('\Graze\Dal\Adapter\GeneratableInterface')) {
                throw new InvalidArgumentException('Adapter must implement \Graze\Dal\Adapter\GeneratableInterface.');
            }

            /** @var GeneratorInterface $generator */
            $generator = $adapterName::buildRecordGenerator([$entityName => $entityConfig]);
            $records += $generator->generate();
        }

        $rootNamespace = $input->getArgument('root-namespace');
        $rootNamespace = rtrim($rootNamespace, '\\') . '\\';
        $directory = $input->getArgument('directory');
        $directory = rtrim($directory, '/');

        $this->persistClasses($records, $rootNamespace, $directory, $output);
    }
}
