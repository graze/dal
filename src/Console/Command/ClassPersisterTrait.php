<?php

namespace Graze\Dal\Console\Command;

use Symfony\Component\Console\Output\OutputInterface;

trait ClassPersisterTrait
{
    /**
     * @param array $classes
     * @param string $rootNamespace
     * @param string $directory
     * @param OutputInterface $output
     */
    public function persistClasses(array $classes, $rootNamespace, $directory, OutputInterface $output)
    {
        foreach ($classes as $name => $class) {
            $prefix = '<info>Generated</info>';
            if (class_exists($name)) {
                $prefix = '<fg=yellow>Updated</>';
            }
            $nameWithoutRoot = str_replace($rootNamespace, '', $name);
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
                $output->writeln($prefix . ': <fg=cyan>' . $name . ' -> ' . $filePath . '</>');
            }
        }
    }
}
