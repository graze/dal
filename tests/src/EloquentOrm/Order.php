<?php

namespace Graze\Dal\Test\EloquentOrm;

/**
 * This is a generated record that is managed by DAL, manual changes to this record
 * will be lost if the generate command is ran again. Changes should be made to the
 * config that is managing this record and the generate command ran.
 */
class Order extends \Illuminate\Database\Eloquent\Model
{

    public $table = 'order';

    public $timestamps = false;

    protected $guarded = array(
        'id',
    );

}
