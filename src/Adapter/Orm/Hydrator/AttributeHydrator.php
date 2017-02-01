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
namespace Graze\Dal\Adapter\Orm\Hydrator;

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
    public function __construct($toData = 'toArray', $fromData = 'fromArray')
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
        if (! is_callable([$object, $this->toData])) {
            throw new InvalidEntityException($object, __METHOD__);
        }

        $data = call_user_func([$object, $this->toData]);
        $filter = $this->getFilter();

        foreach ($data as $name => $value) {
            if (! $filter->filter($name)) {
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
        $replacement = [];
        foreach ($data as $key => $value) {
            $name = $this->hydrateName($key, $data);
            $replacement[$name] = $this->hydrateValue($name, $value, $data);
        }

        if (is_callable([$object, $this->fromData])) {
            call_user_func([$object, $this->fromData], $replacement);
        } else {
            throw new InvalidEntityException($object, __METHOD__);
        }

        return $object;
    }
}
