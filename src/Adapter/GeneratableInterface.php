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
