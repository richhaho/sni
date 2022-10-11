<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class ReportSubscribed extends Model
{
    protected $fillable = [
        'report_id','client_id','users','weekdays','time','created_at','updated_at','deleted_at'
    ];
    public $timestamps = false; 
    protected $table='reports_subscribed';

    public function report() {
        return Report::where('id', $this->report_id)->first();
    }  
}
