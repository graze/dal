<?php

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
