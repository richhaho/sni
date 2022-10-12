<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobReminders extends Model
{
    protected $fillable = [
        'emails', 'note', 'date', 'sent_at', 'status', 'job_id', 'created_at', 'updated_at', 'deleted_at',
    ];

    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'job_reminders';

    public function job()
    {
        return Job::where('id', $this->job_id)->first();
    }
}
