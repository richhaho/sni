<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\WorkOrderType
 *
 * @property string $slug
 * @property string $name
 * @property string|null $deleted_at
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderType onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderType whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderType withoutTrashed()
 * @mixin \Eloquent
 */
class WorkOrderType extends Model
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

    public function template()
    {
        return $this->hasOne('App\Template', 'type_slug', 'slug');
    }
}
