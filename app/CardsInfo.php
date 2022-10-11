<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class CardsInfo extends Model
{
    protected $fillable = [
        'client_id', 'payeezt_type', 'payeezy_value', 'payeezy_cardholder_name', 'payeezy_exp_date', 'status'
    ];
    use SoftDeletes;
    public $timestamps = false; 
    protected $table='clients_cardinfo';

}
