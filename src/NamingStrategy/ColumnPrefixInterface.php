<?php

namespace Graze\Dal\NamingStrategy;

/**
 * Enable column prefix on a Model
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
