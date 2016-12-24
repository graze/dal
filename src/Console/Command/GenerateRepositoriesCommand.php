<?php

namespace Graze\Dal\Console\Command;

use Graze\Dal\Generator\RepositoryGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class GenerateRepositoriesCommand extends Command
{
    use ClassPersisterTrait;

    protected function configure()
    {
        $this->setName('generate:repositories')
            ->setDescription('Use provided config to generate repositories.')
            ->addArgument('config', InputArgument::REQUIRED, 'The YAML config file to generate from.')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the repositories (e.g. Acme\\\\Entity).')
            ->addArgument('directory', InputArgument::REQUIRED, 'The source directory for the generated repositories')
            ->addOption('interfaces', null, InputOption::VALUE_NONE, 'Generate an interface for each repository.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $input->getArgument('config');
        $config = (new Parser())->parse(file_get_contents($configPath));

        $generator = new RepositoryGenerator($config, $input->getOption('interfaces'));
        $repositories = $generator->generate();

        $rootNamespace = $input->getArgument('root-namespace');
        $rootNamespace = rtrim($rootNamespace, '\\') . '\\';
        $directory = $input->getArgument('directory');
        $directory = rtrim($directory, '/');

        $this->persistClasses($repositories, $rootNamespace, $directory, $output, false);
    }
}
