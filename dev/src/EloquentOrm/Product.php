<?php

namespace Graze\Dal\Dev\EloquentOrm;

class Product extends \Illuminate\Database\Eloquent\Model
{

    public $table = 'product';

    public $timestamps = false;

    protected $guarded = array(
        'id',
    );

}
