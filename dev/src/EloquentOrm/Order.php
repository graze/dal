<?php

namespace Graze\Dal\Dev\EloquentOrm;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	public $table = 'order';
	public $timestamps = false;
	protected $guarded = ['id'];
}
