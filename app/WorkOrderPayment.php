<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\WorkOrderPayment
 *
 * @property int $id
 * @property int $work_order_id
 * @property int $job_id
 * @property string $reference
 * @property string $description
 * @property float $amount
 * @property string|null $payed_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderPayment onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment wherePayedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\WorkOrderPayment whereWorkOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderPayment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\WorkOrderPayment withoutTrashed()
 * @mixin \Eloquent
 */
class WorkOrderPayment extends Model
{
    use SoftDeletes;
}
