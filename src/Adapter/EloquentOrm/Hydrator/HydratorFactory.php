<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\EloquentOrm\Hydrator;

use GeneratedHydrator\Configuration;
use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratorFactory
{
    /**
     * @param string $entityName
     * @return HydratorInterface
     */
    public function buildEntityHydrator($entityName)
    {
        $config = new Configuration($entityName);
        $class  = $config->createFactory()->getHydratorClass();

        return new $class();
    }

    /**
     * @param string $recordName
     * @return HydratorInterface
     */
    public function buildRecordHydrator($recordName)
    {
        return new PropertyHydrator();
    }
}
