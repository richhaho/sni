<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\AttachmentType
 *
 * @property string $slug
 * @property string $name
 * @property string $type
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\AttachmentType onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AttachmentType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AttachmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AttachmentType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AttachmentType whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AttachmentType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\AttachmentType withoutTrashed()
 * @mixin \Eloquent
 */
class AttachmentType extends Model
{
    use SoftDeletes;
    
    public $timestamps = false; 
        /**
     * primaryKey 
     * 
     * @var integer
     * @access protected
     */
    protected $primaryKey = 'slug';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
