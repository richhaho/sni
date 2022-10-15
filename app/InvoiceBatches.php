<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceBatches extends Model
{
    protected $fillable = [
        'client_id', 'invoice_id', 'payed_at', 'payment_id', 'created_at',
    ];

    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'invoice_batches';

    public function invoices()
    {
        $invoices_id = unserialize($this->invoice_id);

        return Invoice::find($invoices_id);
    }

    public function first_invoice()
    {
        return Invoice::where('batch_id', $this->id)->orderBy('created_at')->first();
    }

    public function client()
    {
        return Client::where('id', $this->client_id)->first();
    }
}
