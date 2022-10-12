<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Storage;

/**
 * App\Attachment
 *
 * @property int $id
 * @property string $type
 * @property string|null $description
 * @property string|null $file_path
 * @property string|null $file_mime
 * @property string|null $thumb_path
 * @property string|null $original_name
 * @property int $file_size
 * @property int $user_id
 * @property int $generated_id
 * @property \Carbon\Carbon|null $printed_at
 * @property int $attachable_id
 * @property string $attachable_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $resent
 * @property \Carbon\Carbon|null $resent_at
 * @property int $resent_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $attachable
 * @property-read \App\WorkOrderRecipient $recipient
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereAttachableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereAttachableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereFileMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereGeneratedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment wherePrintedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereResent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereResentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereResentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereThumbPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Attachment whereUserId($value)
 * @mixin \Eloquent
 */
class Attachment extends Model
{
    protected $dates = ['printed_at', 'resent_at'];

    public static function boot()
    {
        parent::boot();

        Attachment::deleted(function ($attachment) {
            $file = $attachment->file_path;
            $thumb_file = $attachment->thumb_path;
            Storage::delete($file);
            Storage::delete($thumb_file);
        });
    }

    use Searchable;

    public function attachable()
    {
        return $this->morphTo();
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['file_mime']);
        unset($array['thumb_path']);
        unset($array['file_size']);
        unset($array['user_id']);
        unset($array['generated_id']);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['pinted_at']);
        unset($array['attachable_id']);
        unset($array['attachable_type']);

        return $array;
    }

    public function recipient()
    {
        return $this->belongsTo(\App\WorkOrderRecipient::class, 'generated_id');
    }
}
