<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BatchDetail
 *
 * @property int $id
 * @property int $batch_id
 * @property int $work_order_recipient
 * @property int $attachment_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $client_id
 * @property-read \App\Attachment $attachment
 * @property-read \App\MailingBatch $batch
 * @property-read \App\WorkOrderRecipient $recipient
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereAttachmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BatchDetail whereWorkOrderRecipient($value)
 * @mixin \Eloquent
 */
class BatchDetail extends Model
{
    public function batch()
    {
        return $this->belongsTo('App\MailingBatch', 'batch_id');
    }

    public function recipient()
    {
        return $this->belongsTo('App\WorkOrderRecipient', 'work_order_recipient');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Attachment');
    }
}
