<?php

namespace Graze\Dal\Console\Command;

use Graze\Dal\Console\Persister\ClassPersister;
use Graze\Dal\Generator\EntityGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class GenerateEntitiesCommand extends Command
{
    /**
     * @var ClassPersister
     */
    private $classPersister;

    /**
     * @param ClassPersister $classPersister
     * @param string $name
     */
    public function __construct(ClassPersister $classPersister, $name = null)
    {
        parent::__construct($name);
        $this->classPersister = $classPersister;
    }

    protected function configure()
    {
        $this->setName('generate:entities')
            ->setDescription('Use provided config to generate entities.')
            ->addArgument('config', InputArgument::REQUIRED, 'The YAML config file to generate from.')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the entities (e.g. App\\\\Dal).')
            ->addArgument('directory', InputArgument::REQUIRED, 'The source directory for the generated entities')
            ->addOption('no-getters', null, InputOption::VALUE_NONE, 'Do not generate getter methods.')
            ->addOption('no-setters', null, InputOption::VALUE_NONE, 'Do not generate setter methods.')
            ->addOption('interfaces', null, InputOption::VALUE_NONE, 'Generate an interface for each entity.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->classPersister->setOutput($output);

        $configPath = $input->getArgument('config');
        $config = (new Parser())->parse(file_get_contents($configPath));
        $getters = ! $input->getOption('no-getters');
        $setters = ! $input->getOption('no-setters');
        $interfaces = $input->getOption('interfaces');

        $generator = new EntityGenerator($config, $getters, $setters, $interfaces);
        $entities = $generator->generate();

        $rootNamespace = $input->getArgument('root-namespace');
        $rootNamespace = rtrim($rootNamespace, '\\') . '\\';
        $directory = $input->getArgument('directory');
        $directory = rtrim($directory, '/');

        $this->classPersister->persist($entities, $rootNamespace, $directory);
    }
}
