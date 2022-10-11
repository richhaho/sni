<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Reminders extends Model
{
    protected $fillable = [
        'reminder_name','email_message', 'sms_message', 'first_send_date','send_frequency','end_send_date','next_send_date','status','email_subject'
    ];
    use SoftDeletes;
    public $timestamps = false; 
    protected $table='reminders';

}
