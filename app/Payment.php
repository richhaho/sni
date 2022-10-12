<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Payment
 *
 * @property int $id
 * @property string|null $invoices_id
 * @property string|null $type
 * @property float $amount
 * @property int|null $client_id
 * @property string|null $reference
 * @property string|null $gateway
 * @property string|null $transaction_status
 * @property string|null $log_result
 * @property int|null $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereInvoicesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereLogResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereTransactionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereUserId($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
}
