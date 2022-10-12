<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\InvoiceLine
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property int $quantity
 * @property float $price
 * @property float $amount
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Invoice $invoice
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InvoiceLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceLine extends Model
{
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }
}
