<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['county','name', 'url'];
    protected $table='sites';
}
