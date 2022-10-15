<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardTokenize extends Model
{
    protected $fillable = [
        'client_id', 'client_token', 'status', 'description', 'clients_cardinfo_id',
    ];

    public $timestamps = false;

    protected $table = 'card_tokenize';
}
