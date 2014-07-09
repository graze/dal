<?php
namespace Graze\Dal\Adapter\ActiveRecord\Hydrator;

use Graze\Dal\Exception\InvalidEntityException;
use ReflectionClass;
use Zend\Stdlib\Hydrator\ArraySerializable;

class AttributeHydrator extends ArraySerializable
{
    protected $fromData;
    protected $toData;

    /**
     * @param string $toData
     * @param string $fromData
     */
    public function __construct($toData, $fromData)
    {
        $this->toData = $toData;
        $this->fromData = $fromData;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        if (!is_callable(array($object, $this->toData))) {
            throw new InvalidEntityException($object, __METHOD__);
        }

        $data = $object->toArray();
        $filter = $this->getFilter();

        foreach ($data as $name => $value) {
            if (!$filter->filter($name)) {
                unset($data[$name]);
                continue;
            }

            $extractedName = $this->extractName($name, $object);

            if ($extractedName !== $name) {
                unset($data[$name]);
                $name = $extractedName;
            }

            $data[$name] = $this->extractValue($name, $value, $object);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        $replacement = array();
        foreach ($data as $key => $value) {
            $name = $this->hydrateName($key, $data);
            $replacement[$name] = $this->hydrateValue($name, $value, $data);
        }

        $object->loadArray($replacement);

        if (is_callable(array($object, $this->fromData))) {
            $object->loadArray($replacement);
        } else {
            throw new InvalidEntityException($object, __METHOD__);
        }

        return $object;
    }
}
