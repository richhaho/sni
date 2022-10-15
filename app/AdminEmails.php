<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminEmails extends Model
{
    protected $fillable = ['class', 'name', 'users'];

    protected $table = 'admin_emails';
}
