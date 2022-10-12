<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAddressSearchAPI extends Model
{
    protected $fillable = [
        'job_id', 'address_sent', 'json_response', 'updated_at', 'created_at', 'deleted_at',
    ];

    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'job_address_searchapi';
}
