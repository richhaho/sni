<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coordinate extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','client_id', 'address','lat', 'lng','status'];
    protected $table='coordinates';

    public function jobs()
    {
        return Job::where('coordinate_id', $this->id)->where('deleted_at', null)->get();
    }
    
    public function client()
    {
        return Client::where('id', $this->clinet_id)->first();
    }
    public function getFullNameAttribute() {
        return $this->name.' ('.$this->lat.','.$this->lng.')';
    }
}
