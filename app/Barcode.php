<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    protected $fillable = ['code'];

    protected $table = 'barcodes';
}
