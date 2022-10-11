<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class JobLog extends Model
{
    protected $fillable = [
        'job_id', 'user_id', 'user_name','edited_at', 'data', 'type'
    ];

    public $timestamps = false; 
    protected $table='job_logs';

}
