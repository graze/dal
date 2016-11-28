<?php

namespace Graze\Dal\Console\Command;

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
            ->addArgument('adapter', InputArgument::REQUIRED, 'The name of the adapter to generate for (DoctrineOrm, EloquentOrm).')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the entities (e.g. Acme\\\\Entity).')
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

        $adapter = $input->getArgument('adapter');

        $reflectionClass = new \ReflectionClass($adapter);
        if (! $reflectionClass->implementsInterface('\Graze\Dal\Adapter\GeneratableInterface')) {
            throw new InvalidArgumentException('Adapter must implement \Graze\Dal\Adapter\GeneratableInterface.');
        }

        $generator = $adapter::buildRecordGenerator($config);
        $records = $generator->generate();

        $rootNamespace = $input->getArgument('root-namespace');
        $rootNamespace = rtrim($rootNamespace, '\\') . '\\';
        $directory = $input->getArgument('directory');
        $directory = rtrim($directory, '/');

        $this->persistClasses($records, $rootNamespace, $directory, $output);
    }
}
