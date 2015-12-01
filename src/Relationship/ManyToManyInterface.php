<?php

namespace Graze\Dal\Relationship;

interface ManyToManyInterface
{
    /**
     * @param string $table
     * @param array $data
     */
    public function insert($table, array $data);

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchCol($sql, array $bindings);
}
