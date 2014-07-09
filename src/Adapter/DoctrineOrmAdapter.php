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
namespace Graze\Dal\Adapter;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Graze\Dal\Exception\UndefinedRepositoryException;

class DoctrineOrmAdapter implements AdapterInterface
{
    protected $em;

    /**
     * @param EntityManagerInterface
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object $entity
     * @return string
     */
    public function getEntityName($entity)
    {
        return ClassUtils::getClass($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($name)
    {
        try {
            return $this->em->getRepository($name);
        } catch (MappingException $e) {
            throw new UndefinedRepositoryException($name, __METHOD__, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasRepository($name)
    {
        try {
            $this->getRepository($name);
        } catch (UndefinedRepositoryException $e) {
            return false;
        }

        return true;
    }

    /**
     * @{inheritdoc}
     */
    public function flush($entity = null)
    {
        if (null !== $entity) {
            $this->em->flush($entity);
        } else {
            $this->em->flush();
        }
    }

    /**
     * @{inheritdoc}
     */
    public function persist($entity)
    {
        $this->em->persist($entity);
    }

    /**
     * @{inheritdoc}
     */
    public function refresh($entity)
    {
        $this->em->refresh($entity);
    }

    /**
     * @{inheritdoc}
     */
    public function beginTransaction()
    {
        $this->em->getConnection()->beginTransaction();
    }

    /**
     * @{inheritdoc}
     */
    public function commit()
    {
        $this->em->getConnection()->commit();
    }

    /**
     * @{inheritdoc}
     */
    public function rollback()
    {
        $this->em->getConnection()->rollback();
    }
}