<?php

namespace Graze\Dal\Test\Eloquent\Admin;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $table = 'user';
    protected $connection = 'admin';
    protected $primaryKey = 'us_id';
}
