<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * App\ContactInfo
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $city
 * @property string|null $county
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $fax
 * @property string|null $email
 * @property int $primary
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $entity_id
 * @property int $is_hot
 * @property int $hot_id
 * @property int $sni_client
 * @property int $use_on_client
 * @property-read \App\Entity $entity
 * @property-read mixed $address_no_country
 * @property-read mixed $full_address
 * @property-read mixed $full_name
 * @property-read mixed $full_name_or_entity
 * @property-read mixed $name_entity_name
 * @property-read mixed $search_string
 * @property-read \App\ContactInfo $linked
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ContactInfo[] $links
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo enable()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo hot()
 * @method static \Illuminate\Database\Query\Builder|\App\ContactInfo onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo searchByKeyword($keyword)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereHotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereSniClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereUseOnClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ContactInfo whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContactInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ContactInfo withoutTrashed()
 * @mixin \Eloquent
 */
class ContactInfo extends Model
{
    use SoftDeletes;
    use Searchable;

    protected $fillable = ['first_name', 'last_name', 'gender', 'address_1', 'address_2', 'city', 'state', 'zip', 'country', 'phone', 'mobile', 'fax', 'email', 'source', 'source_date'];

    protected $appends = ['full_name', 'full_address', 'name_entity_name', 'is_hot'];

    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFullNameOrEntityAttribute()
    {
        $xname = trim($this->first_name.' '.$this->last_name);
        if (strlen($xname) == 0) {
            return $this->entity->firm_name;
        } else {
            return $xname;
        }
    }

    public function getIsHotAttribute()
    {
        if ($this->entity) {
            return $this->entity->client_id == 0 ? 1 : 0;
        } else {
            return 0;
        }
    }

    public function getFullAddressAttribute()
    {
        $lines = [];
        $line2 = [];
        if (strlen($this->address_1) > 0) {
            $lines[] = $this->address_1;
        }
        if (strlen($this->address_2) > 0) {
            $lines[] = $this->address_2;
        }

        if (strlen($this->city) > 0) {
            $line2[] = $this->city;
        }
        if (strlen($this->state) > 0) {
            $line2[] = $this->state;
        }
        if (strlen($this->zip) > 0) {
            $line2[] = $this->zip;
        }
        $lines[] = implode(' ', $line2);
        if (strlen($this->country) > 0) {
            if (strtoupper($this->country) == 'UNITED STATES') {
            } else {
                $lines[] = $this->country;
            }
        }
        $xaddress = implode('<br />', $lines);

        return trim($xaddress);
    }

    public function getAddressNoCountryAttribute()
    {
        $lines = [];
        $line2 = [];
        if (strlen($this->address_1) > 0) {
            $lines[] = $this->address_1;
        }
        if (strlen($this->address_2) > 0) {
            $lines[] = $this->address_2;
        }

        if (strlen($this->city) > 0) {
            $line2[] = $this->city;
        }
        if (strlen($this->state) > 0) {
            $line2[] = $this->state;
        }
        if (strlen($this->zip) > 0) {
            $line2[] = $this->zip;
        }
        $lines[] = implode(' ', $line2);

        $xaddress = implode('<br />', $lines);

        return trim($xaddress);
    }

    public function scopeEnable($query)
    {
        return $query->where('status', 1);
    }

    public function getSearchStringAttribute()
    {
        return str_replace('<br />', ' - ', $this->full_address);
    }

    public function getNameEntityNameAttribute()
    {
        if ($this->entity) {
            if ($this->full_name == $this->entity->firm_name) {
                return trim($this->full_name);
            }
            if (strlen($this->full_name) > 0) {
                return trim($this->entity->firm_name.' ('.$this->full_name.')');
            } else {
                return trim($this->entity->firm_name);
            }
        } else {
            return trim($this->full_name);
        }
    }

    public function entity()
    {
        return $this->belongsTo('App\Entity')->withTrashed();
    }

    public static function scopeSearchByKeyword($query, $keyword)
    {
        if ($keyword != '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('address_1', 'LIKE', "%$keyword%")
                    ->orWhere('address_2', 'LIKE', "%$keyword%")
                    ->orWhere('state', 'LIKE', "%$keyword%")
                    ->orWhere('city', 'LIKE', "%$keyword%")
                    ->orWhere('country', 'LIKE', "%$keyword%");
            })->orWhereHas('entity', function ($q) use ($keyword) {
                $q->where('firm_name', 'like', "%$keyword%");
            });
        }

        return $query;
    }

    public function scopeHot($query)
    {
        return $query->whereHas('entity', function ($q) {
            $q->where('client_id', '=', 0);
        });
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['gender']);
        unset($array['primary']);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        unset($array['entity_id']);
        unset($array['is_hot']);
        unset($array['hot_id']);
        unset($array['full_name']);
        unset($array['full_address']);
        unset($array['name_entity_name']);
        unset($array['entity']);

        return $array;
    }

    public function linked()
    {
        return $this->belongsTo('App\ContactInfo', 'hot_id');
    }

    public function links()
    {
        return $this->hasMany('App\ContactInfo', 'hot_id');
    }
}
