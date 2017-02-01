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
namespace Graze\Dal\NamingStrategy;

/**
 * Enable column prefix on a Model
 *
 * @deprecated - DAL 0.x
 */
interface ColumnPrefixInterface
{
    /**
     * Return a prefix for all columns in a table
     *
     * @return string   The prefix (e.g. 'ts_')
     */
    public function getColumnPrefix();
}
