<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * App\Note
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $note_text
 * @property int $noteable_id
 * @property string $noteable_type
 * @property int $entered_by
 * @property int $client_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $entered_at
 * @property string|null $readed_at
 * @property int|null $viewable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $noteable
 * @property-read \App\User $writer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note forClients()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Note onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereEnteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereEnteredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNoteText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNoteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereNoteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereReadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Note whereViewable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Note withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Note withoutTrashed()
 * @mixin \Eloquent
 */
class Note extends Model
{
    use SoftDeletes;
    use Searchable;

    public function writer()
    {
        return $this->belongsTo('App\User', 'entered_by');
    }

    public function noteable()
    {
        return $this->morphTo();
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['type']);
        unset($array['noteable_id']);
        unset($array['noteable_type']);
        unset($array['entered_by']);
        unset($array['client_id']);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        unset($array['entered_at']);
        unset($array['readed_at']);
        unset($array['viewable']);

        return $array;
    }

    public function scopeForClients($q)
    {
        return $q->where('viewable', 1);
    }
}
