<?php

namespace App;

use Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * App\User
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property int $status
 * @property int $client_id
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $email_token
 * @property int $verified
 * @property-read \App\Client $client
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User isRole($role)
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait { restore as private restore_entrust; }
    use SoftDeletes { restore as private restore_softdelete; }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->email_token = str_random(30);
            $user->status = 0;
            $user->verified = false;
        });
    }

    public function restore()
    {
        $this->restore_entrust();
        $this->restore_softdelete();
    }

    public function confirmEmail()
    {
        $this->verified = true;
        $this->email_token = null;
        $this->save();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'status', 'client_id', 'verified', 'restricted',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //public function setPasswordAttribute($password)
    //{
    //    $this->attributes['password'] = Hash::make($password);
    //}

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function scopeIsRole($query, $role)
    {
        return $query->whereHas(
            'roles', function ($query) use ($role) {
                if (is_array($role)) {
                    $query->whereIn('name', $role);
                } else {
                    $query->where('name', $role);
                }
            }
        );
    }

    public function client()
    {
        return $this->belongsTo(\App\Client::class);
    }
}
