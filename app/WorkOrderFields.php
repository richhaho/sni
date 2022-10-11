<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class WorkOrderFields extends Model
{
    protected $fillable = [
        'workorder_type','required', 'field_order', 'field_type','field_label','created_at'
    ];
    use SoftDeletes;
    public $timestamps = false; 
    protected $table='work_order_fields';

}
