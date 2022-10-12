<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FromEmails extends Model
{
    protected $fillable = ['class', 'name', 'from_email', 'from_name'];

    protected $table = 'from_emails';
}
