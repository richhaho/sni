<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Report extends Model
{
    protected $fillable = [
        'folder_id','name','client_type','sql','created_at','updated_at','deleted_at'
    ];
    use SoftDeletes;
    public $timestamps = false; 
    protected $table='reports';

    public function folder() {
        return Folder::where('id', $this->folder_id)->first();
    }  
}
