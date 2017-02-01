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
