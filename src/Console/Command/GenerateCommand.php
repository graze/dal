<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate')
            ->setDescription('Run all generators using the provided config.')
            ->addArgument('config', InputArgument::REQUIRED, 'The YAML config to generate from.')
            ->addArgument('root-namespace', InputArgument::REQUIRED, 'The root namespace for the directory where the files will be generated')
            ->addArgument('directory', InputArgument::REQUIRED, 'The source directory for the generated files')
            ->addOption('interfaces', null, InputOption::VALUE_NONE, 'Generate interfaces for the entities and repositories')
            ->addOption('no-getters', null, InputOption::VALUE_NONE, 'Do not generate getter methods for entities.')
            ->addOption('no-setters', null, InputOption::VALUE_NONE, 'Do not generate setter methods for entities.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entitiesCommand = $this->getApplication()->find('generate:entities');
        $repositoriesCommand = $this->getApplication()->find('generate:repositories');
        $recordsCommand = $this->getApplication()->find('generate:records');

        $entityArguments = [
            'config' => $input->getArgument('config'),
            'root-namespace' => $input->getArgument('root-namespace'),
            'directory' => $input->getArgument('directory'),
            '--no-getters' => $input->getOption('no-getters'),
            '--no-setters' => $input->getOption('no-setters'),
            '--interfaces' => $input->getOption('interfaces')
        ];

        $entityInput = new ArrayInput($entityArguments);
        $entitiesCommand->run($entityInput, $output);

        $repositoryArguments = [
            'config' => $input->getArgument('config'),
            'root-namespace' => $input->getArgument('root-namespace'),
            'directory' => $input->getArgument('directory'),
            '--interfaces' => $input->getOption('interfaces')
        ];

        $repositoryInput = new ArrayInput($repositoryArguments);
        $repositoriesCommand->run($repositoryInput, $output);

        $recordsArguments = [
            'config' => $input->getArgument('config'),
            'root-namespace' => $input->getArgument('root-namespace'),
            'directory' => $input->getArgument('directory'),
        ];

        $recordsInput = new ArrayInput($recordsArguments);
        $recordsCommand->run($recordsInput, $output);
    }
}
