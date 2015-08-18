<?php

namespace Graze\Dal\Test\Eloquent\Admin;

use Graze\Dal\NamingStrategy\ColumnPrefixInterface;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements ColumnPrefixInterface
{
    public $table = 'user';
    protected $connection = 'admin';
    protected $primaryKey = 'us_id';

    /**
     * Return a prefix for all columns in a table
     *
     * @return string   The prefix (e.g. 'ts_')
     */
    public function getColumnPrefix()
    {
        return 'us_';
    }
}
