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
namespace Graze\Dal\Adapter\Orm\EloquentOrm\Generator;

use Graze\Dal\Generator\GeneratorInterface;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\PropertyGenerator;

class RecordGenerator implements GeneratorInterface
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
        $records = [];

        foreach ($this->config as $entityName => $config) {
            $recordName = $config['record'];

            $record = new ClassGenerator($recordName);
            $record->setDocBlock(DocBlockGenerator::fromArray([
                'longdescription' => 'This is a generated record that is managed by DAL, manual changes to this record will be lost if the generate command is ran again. Changes should be made to the config that is managing this record and the generate command ran.'
            ]));
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
