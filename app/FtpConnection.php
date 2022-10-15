<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FtpConnection
 *
 * @property int $id
 * @property string|null $ftp_name
 * @property string|null $ftp_host
 * @property string|null $ftp_user
 * @property string|null $ftp_password
 * @property string|null $ftp_path
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereFtpHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereFtpName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereFtpPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereFtpPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereFtpUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FtpConnection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FtpConnection extends Model
{
    protected $fillable = ['ftp_name', 'ftp_host', 'ftp_user', 'ftp_password', 'ftp_path'];
}
