<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $perms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends EntrustRole
{
    protected $fillable = ['name', 'display_name', 'type', 'description'];
}
