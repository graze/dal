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
namespace Graze\Dal\Generator;

use GeneratedHydrator\Configuration;
use Graze\Dal\DalManagerInterface;

class HydratorGenerator implements GeneratorInterface
{
    /**
     * @var DalManagerInterface
     */
    private $dm;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * @param DalManagerInterface $dm
     * @param string $targetDir
     */
    public function __construct(DalManagerInterface $dm, $targetDir)
    {
        $this->dm = $dm;
        $this->targetDir = $targetDir;
    }

    /**
     * @return mixed
     */
    public function generate()
    {
        $adapters = $this->dm->all();

        foreach ($adapters as $adapter) {
            $entityNames = $adapter->getConfiguration()->getEntityNames();

            foreach ($entityNames as $entityName) {
                $config = new Configuration($entityName);
                $config->setGeneratedClassesTargetDir($this->targetDir);
                $config->createFactory()->getHydratorClass();
            }
        }
    }
}
