<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FtpLocation
 *
 * @property int $id
 * @property string $name
 * @property string $path
 * @property int $connection_id
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\FtpConnection $server
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FtpLocation extends Model
{
    protected $fillable = ['name', 'path', 'connection_id'];

    public function server()
    {
        return $this->belongsTo('\App\FtpConnection', 'connection_id');
    }
}
