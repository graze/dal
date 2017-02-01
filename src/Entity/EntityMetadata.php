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
namespace Graze\Dal\Entity;

use Graze\Dal\Configuration\ConfigurationInterface;

class EntityMetadata
{
    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * @param EntityInterface $entity
     * @param ConfigurationInterface $config
     */
    public function __construct(EntityInterface $entity, ConfigurationInterface $config)
    {
        $this->entity = $entity;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getRelationshipMetadata()
    {
        $mapping = $this->getMapping();
        if (array_key_exists('related', $mapping)) {
            return $mapping['related'];
        }

        return [];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRelationship($name)
    {
        return array_key_exists($name, $this->getRelationshipMetadata());
    }

    /**
     * @return array
     */
    private function getMapping()
    {
        return $this->config->getMapping($this->config->getEntityName($this->entity));
    }
}
