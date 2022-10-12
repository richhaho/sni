<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * App\Entity
 *
 * @property int $id
 * @property string|null $firm_name
 * @property string|null $latest_type
 * @property int $client_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $is_hot
 * @property int $hot_id
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ContactInfo[] $contacts
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity hot()
 * @method static \Illuminate\Database\Query\Builder|\App\Entity onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereFirmName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereHotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereLatestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Entity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Entity withoutTrashed()
 * @mixin \Eloquent
 */
class Entity extends Model
{
    use Searchable;
    use SoftDeletes;

//
    protected $fillable = ['firm_name', 'latest_type', 'client_id'];

    public function contacts()
    {
        return $this->hasMany(\App\ContactInfo::class)->withTrashed();
    }

    public function scopeHot($query)
    {
        return $query->where('client_id', '=', 0);
    }

    public function client()
    {
        return $this->belongsTo(\App\Client::class)->withTrashed();
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['latest_type']);
        unset($array['client_id']);

        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        unset($array['is_hot']);
        unset($array['hot_id']);

        return $array;
    }
}
