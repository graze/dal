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

use Graze\Dal\Exception\InvalidEntityException;
use ReflectionClass;
use Zend\Stdlib\Hydrator\ArraySerializable;

class PropertyHydrator extends ArraySerializable
{
    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        if (!is_callable(array($object, 'toArray'))) {
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
            // replace the original key with extracted, if differ
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

        if (is_callable(array($object, 'fill'))) {
            $object->loadArray($replacement);
        } else {
            throw new InvalidEntityException($object, __METHOD__);
        }
        return $object;
    }
}
