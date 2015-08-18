<?php

namespace Graze\Dal\Test\Eloquent\Account;

use Graze\Dal\NamingStrategy\ColumnPrefixInterface;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model implements ColumnPrefixInterface
{
    public $table = 'account_profile';
    protected $primaryKey = 'ap_id';

    /**
     * Return a prefix for all columns in a table
     *
     * @return string   The prefix (e.g. 'ts_')
     */
    public function getColumnPrefix()
    {
        return 'ap_';
    }
}
