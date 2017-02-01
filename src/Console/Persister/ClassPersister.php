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
namespace Graze\Dal\Console\Persister;

use Symfony\Component\Console\Output\OutputInterface;

class ClassPersister
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->output = $output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param array $classes
     * @param string $rootNamespace
     * @param string $directory
     * @param bool $overwrite
     */
    public function persist(array $classes, $rootNamespace, $directory, $overwrite = true)
    {
        foreach ($classes as $className => $class) {
            if (! $overwrite && class_exists($className)) {
                continue;
            }

            $prefix = '<info>Generated</info>';
            if (class_exists($className)) {
                $prefix = '<fg=yellow>Updated</>';
            }

            $filePath = $this->getFilePath($className, $rootNamespace, $directory);

            $isUpdated = false;
            if (file_exists($filePath)) {
                $oldContents = file_get_contents($filePath);
                if ($oldContents !== $class) {
                    $isUpdated = true;
                }
            } else {
                $isUpdated = true;
            }

            if ($isUpdated) {
                file_put_contents($filePath, $class);
                if ($this->output) {
                    $this->output->writeln($prefix . ': <fg=cyan>' . $className . ' -> ' . $filePath . '</>');
                }
            }
        }
    }

    /**
     * @param string $className
     * @param string $rootNamespace
     * @param string $directory
     *
     * @return string
     */
    private function getFilePath($className, $rootNamespace, $directory)
    {
        $nameWithoutRoot = str_replace($rootNamespace, '', $className);
        $nameBits = explode('\\', $nameWithoutRoot);
        $filename = array_pop($nameBits) . '.php';

        $filePath = getcwd() . '/' . $directory;
        foreach ($nameBits as $bit) {
            $filePath .= '/' . $bit;
            if (! is_dir($filePath)) {
                mkdir($filePath);
            }
        }
        $filePath .= '/' . $filename;

        return $filePath;
    }
}
