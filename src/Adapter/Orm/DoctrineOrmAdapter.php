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
namespace Graze\Dal\Adapter\Orm;

use Closure;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Exception;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Exception\UndefinedRepositoryException;
use PDO;

class DoctrineOrmAdapter extends OrmAdapter
{
    protected $em;

    /**
     * @param ConfigurationInterface $config
     * @param EntityManagerInterface $em
     */
    public function __construct(ConfigurationInterface $config, EntityManagerInterface $em)
    {
        parent::__construct($config);
        $this->em = $em;
    }

    /**
     * @param object $entity
     *
     * @return string
     */
//    public function getEntityName($entity)
//    {
//        return ClassUtils::getClass($entity);
//    }

    /**
     * {@inheritdoc}
     */
//    public function getRepository($name)
//    {
//        try {
//            return $this->em->getRepository($name);
//        } catch (MappingException $e) {
//            throw new UndefinedRepositoryException($name, __METHOD__, $e);
//        }
//    }

    /**
     * {@inheritdoc}
     */
//    public function hasRepository($name)
//    {
//        try {
//            $this->getRepository($name);
//        } catch (UndefinedRepositoryException $e) {
//            return false;
//        }
//
//        return true;
//    }

    /**
     * @{inheritdoc}
     */
//    public function flush($entity = null)
//    {
//        if (null !== $entity) {
//            $this->em->flush($entity);
//        } else {
//            $this->em->flush();
//        }
//    }
//
//    /**
//     * @{inheritdoc}
//     */
//    public function persist($entity)
//    {
//        $this->em->persist($entity);
//    }
//
//    /**
//     * @{inheritdoc}
//     */
//    public function refresh($entity)
//    {
//        $this->em->refresh($entity);
//    }
//
//    /**
//     * @{inheritdoc}
//     */
//    public function remove($entity)
//    {
//        $this->em->remove($entity);
//    }

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

    /**
     * @{inheritdoc}
     */
    public function transaction(callable $fn)
    {
        if (! $fn instanceof Closure) {
            $fn = function ($adapter) use ($fn) {
                call_user_func($fn, $adapter);
            };
        }

        $this->beginTransaction();

        try {
            $fn($this);
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return mixed
     */
    public function fetch($sql, array $bindings = [])
    {
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchOne($sql, array $bindings = [])
    {
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param array $data
     */
    public function insert($table, array $data)
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_values($data));
        $sql = "INSERT INTO `?` (?) VALUES (?)";
        $bindings = [
            $table,
            rtrim($fields, ', '),
            rtrim($values, ', ')
        ];

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($bindings);
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchCol($sql, array $bindings = [])
    {
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchColumn(0);
    }
}
