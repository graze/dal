<?php

namespace Graze\Dal\NamingStrategy;

class CombinedNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var NamingStrategyInterface[][]
     */
    protected $strategies = [];

    /**
     * Add a naming strategy to be used
     *
     * @param  NamingStrategyInterface $strategy
     * @param  int                     $priority Where this strategy should be used
     * @return bool
     */
    public function addNamingStrategy(NamingStrategyInterface $strategy, $priority = 99)
    {
        if (!$this->hasNamingStrategy($strategy)) {
            $this->strategies[] = [
                'strategy' => $strategy,
                'priority' => $priority
            ];

            // eager sorting on add
            usort(
                $this->strategies,
                function ($a, $b) {
                    return ($a['priority'] == $b['priority']) ? 0
                    : ($a['priority'] < $b['priority']) ? -1 : 1;
                }
            );

            return true;
        }
        return false;
    }

    /**
     * Determine if a specific strategy has been added
     *
     * @param  NamingStrategyInterface $strategy
     * @return bool
     */
    public function hasNamingStrategy(NamingStrategyInterface $strategy)
    {
        return $this->getIndex($strategy) >= 0;
    }

    /**
     * Return this index of a strategy
     *
     * @param  NamingStrategyInterface $strategy
     * @return int The index or -1 if not found
     */
    private function getIndex(NamingStrategyInterface $strategy)
    {
        for ($i = 0; $i < count($this->strategies); $i++) {
            if ($this->strategies[$i]['strategy'] === $strategy) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * Remove a specified naming strategy from the list
     *
     * @param  NamingStrategyInterface $strategy
     * @return bool
     */
    public function removeNamingStrategy(NamingStrategyInterface $strategy)
    {
        if (($index = $this->getIndex($strategy)) != -1) {
            unset($this->strategies[$index]);
            return true;
        }
        return false;
    }

    /**
     * Loop through each naming strategy in priority order
     *
     * @param  string      $name
     * @param  object|null $object
     * @return string
     */
    public function hydrate($name, $object = null)
    {
        $hydratedName = $name;
        foreach ($this->strategies as $strategy) {
            $hydratedName = $strategy['strategy']->hydrate($hydratedName, $object);
        }
        return $hydratedName;
    }

    /**
     * Loop through each naming strategy in priority order
     *
     * @param  string     $name
     * @param  array|null $data
     * @return string
     */
    public function extract($name, $data = null)
    {
        $extractedName = $name;
        foreach ($this->strategies as $strategy) {
            $extractedName = $strategy['strategy']->extract($extractedName, $data);
        }
        return $extractedName;
    }

    /**
     * @param string|object $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return true;
    }
}
