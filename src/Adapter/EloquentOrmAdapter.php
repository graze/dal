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

use Graze\Dal\Adapter\EloquentOrm\Configuration;
use Illuminate\Database\ConnectionInterface;

class EloquentOrmAdapter extends ActiveRecordAdapter
{
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
}
