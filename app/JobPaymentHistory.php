<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\JobPaymentHistory
 *
 * @property int $id
 * @property int|null $job_id
 * @property string|null $payed_on
 * @property string|null $description
 * @property float $amount
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Job|null $job
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory wherePayedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobPaymentHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class JobPaymentHistory extends Model
{
    protected $fillable = ['description', 'amaount', 'payed_on', 'attached_file'];

    public function job()
    {
        return $this->belongsTo('App\Job');
    }
}
