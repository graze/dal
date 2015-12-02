<?php

namespace Graze\Dal\Adapter;

use Graze\Dal\Generator\GeneratorInterface;

interface GeneratableInterface
{
    /**
     * @param array $config
     *
     * @return GeneratorInterface
     */
    public static function buildRecordGenerator(array $config);
}
