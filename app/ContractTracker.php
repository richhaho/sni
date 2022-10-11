<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ContractTracker
 *
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Client $client
  * @mixin \Eloquent
 */
class ContractTracker extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'client_id',
        'contract_file',
        'file_original_name',
        'file_mime',
        'file_size',
        'file_extension',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $table='contract_trackers';
    
    public function client()
    {
        return $this->belongsTo('App\Client')->withTrashed();
    }

    public function job()
    {
        return Job::where('contract_tracker_id', $this->id)->first();
    }
}
