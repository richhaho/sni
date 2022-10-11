<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailingType extends Model
{
    protected $fillable = [
        'type',
        'postage',
        'fee',
        'stc'
    ];
}
