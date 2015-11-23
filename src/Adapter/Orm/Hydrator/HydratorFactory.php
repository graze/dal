<?php

namespace Graze\Dal\Adapter\Orm\Hydrator;

use CodeGenerationUtils\GeneratorStrategy\EvaluatingGeneratorStrategy;
use GeneratedHydrator\Configuration;
use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratorFactory extends AbstractHydratorFactory implements HydratorFactoryInterface
{
    /**
     * @param object $entity
     *
     * @return HydratorInterface
     */
    protected function buildDefaultEntityHydrator($entity)
    {
        $config = new Configuration($this->config->getEntityName($entity));
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        return new $class();
    }

    /**
     * @param object $record
     *
     * @return HydratorInterface
     */
    protected function buildDefaultRecordHydrator($record)
    {
        $config = new Configuration(get_class($record));
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        $class = $config->createFactory()->getHydratorClass();

        return new $class();
    }
}
