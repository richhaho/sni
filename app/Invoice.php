<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Invoice
 *
 * @property int $id
 * @property int $client_id
 * @property int $work_order_id
 * @property float $total_amount
 * @property string $status
 * @property string|null $type
 * @property string|null $due_at
 * @property string|null $payed_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\InvoiceLine[] $PostageLines
 * @property-read \App\Client $client
 * @property-read mixed $number
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\InvoiceLine[] $lines
 * @property-read \App\WorkOrder $work_order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice wherePayedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice whereWorkOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Invoice wizard()
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    public function getNumberAttribute()
    {
        return sprintf('%08d', $this->id);
    }

    public function client()
    {
        return $this->belongsTo('App\Client')->withTrashed();
    }

    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder')->withTrashed();
    }

    public function lines()
    {
        return $this->hasMany('App\InvoiceLine');
    }

    public function todos()
    {
        return Todo::where('invoice_id', $this->id)->get();
    }

    public function PostageLines()
    {
        return $this->hasMany('App\InvoiceLine')->where('description', 'LIKE', '%POSTAGE%');
    }

    public function updateTotal()
    {
        $xtotal = 0;
        foreach ($this->lines as $line) {
            $xtotal += $line->amount;
        }
        $this->total_amount = $xtotal;
        $this->save();

        return true;
    }

    public function scopeWizard($query)
    {
        return $query->where('type', 'client-wizard');
    }
}
