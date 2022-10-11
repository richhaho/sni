<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\JobChangeOrder
 *
 * @property int $id
 * @property int|null $job_id
 * @property string|null $number
 * @property string|null $added_on
 * @property string|null $description
 * @property float $amount
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Job|null $job
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereAddedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobChangeOrder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class JobChangeOrder extends Model
{
    protected $fillable = ['description','amaount','added_on','number', 'attached_file'];
    public function job()
    {
        return $this->belongsTo('App\Job');
    }
}
