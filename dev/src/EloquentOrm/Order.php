<?php

namespace Graze\Dal\Dev\EloquentOrm;

class Order extends \Illuminate\Database\Eloquent\Model
{

    public $table = 'order';

    public $timestamps = false;

    protected $guarded = array(
        'id',
    );

}
