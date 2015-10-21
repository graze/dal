<?php

namespace Graze\Dal\Dev\EloquentOrm;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $table = 'product';
    public $timestamps = false;
    protected $guarded = ['id'];
}
