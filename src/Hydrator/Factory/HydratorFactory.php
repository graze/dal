<?php

namespace Graze\Dal\Hydrator\Factory;

use CodeGenerationUtils\GeneratorStrategy\EvaluatingGeneratorStrategy;
use GeneratedHydrator\Configuration;
use Graze\Dal\Hydrator\ArrayHydrator;
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
     * @param object|array $record
     *
     * @return HydratorInterface
     */
    protected function buildDefaultRecordHydrator($record)
    {
        if (is_object($record)) {
            $config = new Configuration(get_class($record));
            $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
            $class = $config->createFactory()->getHydratorClass();

            return new $class();
        } elseif (is_array($record)) {
            return new ArrayHydrator();
        }
    }
}
