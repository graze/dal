<?php

namespace Graze\Dal\Adapter\Orm;

use Graze\Dal\Adapter\AdapterInterface;

interface OrmAdapterInterface extends AdapterInterface
{
    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetch($sql, array $bindings = []);

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return mixed
     */
    public function fetchOne($sql, array $bindings = []);

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchCol($sql, array $bindings = []);

    public function beginTransaction();

    public function commit();

    public function rollback();

    /**
     * @param callable $fn
     */
    public function transaction(callable $fn);
}
