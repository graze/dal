<?php

namespace Graze\Dal\Adapter\Orm\EloquentOrm\Generator;

use Graze\Dal\Generator\GeneratorInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\PropertyGenerator;

class RecordGenerator implements GeneratorInterface
{
    private $config;

    /**
     * @param $config
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
        $records = [];

        foreach ($this->config as $entityName => $config) {
            $recordName = $config['record'];

            $record = new ClassGenerator($recordName);
            $record->setExtendedClass('\\Illuminate\\Database\\Eloquent\\Model');

            $record->addProperty('table', $config['table']);
            $record->addProperty('timestamps', $config['timestamps']);
            $record->addProperty('guarded', ['id'], PropertyGenerator::FLAG_PROTECTED);

            $file = FileGenerator::fromArray(['classes' => [$record]]);
            $records[$recordName] = rtrim(preg_replace('/\n(\s*\n){2,}/', "\n\n", $file->generate()), "\n") . "\n";
        }

        return $records;
    }
}
