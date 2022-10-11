<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class JobNoc extends Model
{
    protected $fillable = [
        'job_id',
        'noc_number',
        'noc_notes',
        'copy_noc',
        'recorded_at',
        'expired_at'
    ];
    public $timestamps = false; 
    protected $table='job_nocs';

    public function job() {
        return Job::where('id', $this->job_id)->first();
    }  
}
