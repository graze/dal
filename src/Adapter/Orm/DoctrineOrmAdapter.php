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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Graze\Dal\Adapter\GeneratableInterface;
use Graze\Dal\Adapter\Orm\DoctrineOrm\Configuration;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Generator\GeneratorInterface;
use PDO;
use Symfony\Component\Yaml\Parser;

class DoctrineOrmAdapter extends OrmAdapter implements GeneratableInterface
{
    protected $em;

    /**
     * @param ConfigurationInterface $config
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ConfigurationInterface $config)
    {
        parent::__construct($config);
        $this->em = $em;
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

    /**
     * @param EntityManagerInterface $em
     * @param array $yamlPaths
     * @param string $cacheFile
     *
     * @return EntityManagerInterface
     */
    public static function createFromYaml(EntityManagerInterface $em, array $yamlPaths, $cacheFile = null)
    {
        if ($cacheFile && file_exists($cacheFile)) {
            return static::createFromCache($em, $cacheFile);
        }

        $config = [];
        $parser = new Parser();

        foreach ($yamlPaths as $yamlPath) {
            $config = array_merge($config, $parser->parse(file_get_contents($yamlPath)));
        }

        if ($cacheFile) {
            file_put_contents($cacheFile, json_encode($config));
        }

        return static::createFromArray($em, $config);
    }

    /**
     * @param EntityManagerInterface $em
     * @param array $config
     *
     * @return EntityManagerInterface
     */
    public static function createFromArray(EntityManagerInterface $em, array $config)
    {
        return new static($em, new Configuration($em, $config));
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $cacheFile
     *
     * @return EntityManagerInterface
     */
    private static function createFromCache(EntityManagerInterface $em, $cacheFile)
    {
        $config = json_decode(file_get_contents($cacheFile), true);
        return static::createFromArray($em, $config);
    }

    /**
     * @param array $config
     *
     * @return GeneratorInterface
     */
    public static function buildRecordGenerator(array $config)
    {
    }
}
