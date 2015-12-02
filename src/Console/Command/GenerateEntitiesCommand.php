<?php

namespace Graze\Dal\Console\Command;

use Graze\Dal\Generator\EntityGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class GenerateEntitiesCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate:entities')
            ->setDescription('Use provided config to generate entities that do not exist yet.')
            ->addArgument('config', InputArgument::REQUIRED, 'The YAML config file to generate from.')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the entities (e.g. Acme\Entity).')
            ->addArgument('directory', InputArgument::REQUIRED, 'The source directory for the generated entities')
            ->addOption('no-getters', null, InputOption::VALUE_NONE, 'Do not generate getter methods.')
            ->addOption('no-setters', null, InputOption::VALUE_NONE, 'Do not generate setter methods.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force entity creation, overwrite existing entities.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $input->getArgument('config');
        $config = (new Parser())->parse(file_get_contents($configPath));
        $getters = ! $input->getOption('no-getters');
        $setters = ! $input->getOption('no-setters');

        $generator = new EntityGenerator($config, $getters, $setters);
        $entities = $generator->generate();

        $rootNamespace = $input->getArgument('root-namespace');
        $rootNamespace = rtrim($rootNamespace, '\\') . '\\';
        $directory = $input->getArgument('directory');
        $directory = rtrim($directory, '/');

        foreach ($entities as $name => $entity) {
            if (! $input->getOption('force') && class_exists($name)) {
                continue;
            }
            $nameWithoutRoot = str_replace($rootNamespace, '', $name);
            $nameBits = explode('\\', $nameWithoutRoot);
            $filename = array_pop($nameBits) . '.php';

            $filePath = getcwd() . '/' . $directory;
            foreach ($nameBits as $bit) {
                $filePath .= '/' . $bit;
            }
            $filePath .= '/' . $filename;

            file_put_contents($filePath, $entity);
            $output->writeln('<info>Generated: </info><comment>' . $name . ' -> ' . $filePath . '</comment>');
        }
    }
}
