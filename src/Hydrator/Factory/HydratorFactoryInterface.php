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
namespace Graze\Dal\Hydrator\Factory;

use Zend\Stdlib\Hydrator\HydratorInterface;

interface HydratorFactoryInterface
{
    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entity);

    /**
     * @param object|array $record
     *
     * @return HydratorInterface
     */
    public function buildRecordHydrator($record);
}
