<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    protected $fillable = [
        'name', 'created_at', 'updated_at', 'deleted_at',
    ];

    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'folders';

    public function reports()
    {
        return Report::where('folder_id', $this->id)->where('deleted_at', null)->get();
    }
}
