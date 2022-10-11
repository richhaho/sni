<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MailingBatch
 *
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Attachment[] $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BatchDetail[] $details
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MailingBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MailingBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MailingBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MailingBatch whereUserId($value)
 * @mixin \Eloquent
 */
class MailingBatch extends Model
{
    public function details()
    {
        return $this->hasMany('App\BatchDetail','batch_id');
    }
    
    public function attachments()
    {
        return $this->morphMany('App\Attachment', 'attachable');
    }
}
