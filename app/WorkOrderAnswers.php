<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class WorkOrderAnswers extends Model
{
    protected $fillable = [
        'work_order_id','work_order_field_id', 'answer', 'updated_at','created_at'
    ];
    use SoftDeletes;
    public $timestamps = false; 
    protected $table='work_order_answer';

}
