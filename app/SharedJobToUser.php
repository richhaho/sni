<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SharedJobToUser
 *
 * @property int $id
 * @property int $job_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Job $job
 * @property-read \App\User $user
 * @mixin \Eloquent
 */
class SharedJobToUser extends Model
{
    protected $fillable = [
        'job_id','user_id'
    ];

    protected $table='shared_jobs_to_users';
    
    public function job()
    {
        return $this->belongsTo('App\Job')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }
}
