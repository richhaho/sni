<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\NoteType
 *
 * @property string $slug
 * @property string $name
 * @property string|null $deleted_at
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\NoteType onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NoteType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NoteType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NoteType whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\NoteType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\NoteType withoutTrashed()
 * @mixin \Eloquent
 */
class NoteType extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    /**
     * primaryKey
     *
     * @var int
     */
    protected $primaryKey = 'slug';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
