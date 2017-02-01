<?php
/*
 * This file is part of Graze DAL
 *
 * Copyright (c) 2017 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/dal/blob/master/LICENSE
 */
namespace Graze\Dal\Adapter\Orm;

use Graze\Dal\Adapter\GeneratableInterface;
use Graze\Dal\Adapter\Orm\EloquentOrm\Configuration;
use Graze\Dal\Adapter\Orm\EloquentOrm\Generator\RecordGenerator;
use Graze\Dal\Generator\GeneratorInterface;
use Illuminate\Database\ConnectionInterface;
use Symfony\Component\Yaml\Parser;

class EloquentOrmAdapter extends OrmAdapter implements GeneratableInterface
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $conn;

    /**
     * @param ConnectionInterface $conn
     * @param Configuration $config
     */
    public function __construct(ConnectionInterface $conn, Configuration $config)
    {
        $this->conn = $conn;

        parent::__construct($config);
    }

    /**
     * @{inheritdoc}
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * @{inheritdoc}
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * @{inheritdoc}
     */
    public function rollback()
    {
        $this->conn->rollBack();
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return mixed
     */
    public function fetch($sql, array $bindings = [])
    {
        return $this->conn->select($sql, $bindings);
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return mixed
     */
    public function fetchOne($sql, array $bindings = [])
    {
        return $this->conn->selectOne($sql, $bindings);
    }

    /**
     * @param string $table
     * @param array $data
     */
    public function insert($table, array $data)
    {
        $this->conn->table($table)->insert($data);
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchCol($sql, array $bindings = [])
    {
        $result = $this->fetch($sql, $bindings);
        $col = [];

        foreach ($result as $row) {
            $row = (array) $row;
            $col[] = array_values($row)[0];
        }

        return $col;
    }

    /**
     * @param ConnectionInterface $connection
     * @param $configPath
     *
     * @return static
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public static function factory(ConnectionInterface $connection, $configPath)
    {
        $parser = new Parser();
        $config = $parser->parse(file_get_contents($configPath));
        return new static($connection, new Configuration($config));
    }

    /**
     * @param ConnectionInterface $connection
     * @param array $yamlPaths
     * @param string $cacheFile
     *
     * @return EloquentOrmAdapter
     */
    public static function createFromYaml(ConnectionInterface $connection, array $yamlPaths, $cacheFile = null)
    {
        if ($cacheFile && file_exists($cacheFile)) {
            return static::createFromCache($connection, $cacheFile);
        }

        $config = [];
        $parser = new Parser();

        foreach ($yamlPaths as $yamlPath) {
            $config = array_merge($config, $parser->parse(file_get_contents($yamlPath)));
        }

        if ($cacheFile) {
            file_put_contents($cacheFile, json_encode($config));
        }

        return static::createFromArray($connection, $config);
    }

    /**
     * @param ConnectionInterface $connection
     * @param array $config
     *
     * @return EloquentOrmAdapter
     */
    public static function createFromArray(ConnectionInterface $connection, array $config)
    {
        return new static($connection, new Configuration($config));
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $cacheFile
     *
     * @return EloquentOrmAdapter
     */
    private static function createFromCache(ConnectionInterface $connection, $cacheFile)
    {
        $config = json_decode(file_get_contents($cacheFile), true);
        return static::createFromArray($connection, $config);
    }

    /**
     * @param array $config
     *
     * @return GeneratorInterface
     */
    public static function buildRecordGenerator(array $config)
    {
        return new RecordGenerator($config);
    }
}
