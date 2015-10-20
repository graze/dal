<?php

namespace Graze\Dal\Test\Eloquent\Account;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $table = 'account_profile';
    protected $primaryKey = 'ap_id';
}
