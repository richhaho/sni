<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PropertyRecords extends Model
{
     
    public $timestamps = false; 
    protected $table='property_records';

}
