<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PdfPage
 *
 * @property int $id
 * @property string $type
 * @property int|null $work_order_id
 * @property string|null $fields
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\WorkOrder|null $work_order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PdfPage whereWorkOrderId($value)
 * @mixin \Eloquent
 */
class PdfPage extends Model
{
    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder');
    }
}
