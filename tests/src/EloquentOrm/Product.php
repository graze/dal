<?php

namespace Graze\Dal\Test\EloquentOrm;

class Product extends \Illuminate\Database\Eloquent\Model
{

    public $table = 'product';

    public $timestamps = false;

    protected $guarded = array(
        'id',
    );

}
