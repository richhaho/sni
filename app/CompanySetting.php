<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompanySetting
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $apikey
 * @property string|null $apisecret
 * @property string|null $merchant_token
 * @property string|null $js_security_key
 * @property string|null $ta_token
 * @property string|null $payeezy_mode
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereApikey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereApisecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereJsSecurityKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereMerchantToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting wherePayeezyMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereTaToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompanySetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompanySetting extends Model
{
    //
    protected $fillable = ['name','address','apikey','apisecret','merchant_token','js_security_key','ta_token'];
    
    public function getApiurlAttribute() {
        if($this->payeezy_mode == 'live') {
            return 'prod.api.firstdata.com';
        } else {
            return 'cert.api.firstdata.com';
        }
    }
    public function getUrlAttribute() {
        if($this->payeezy_mode == 'live') {
            return 'api.payeezy.com';
        } else {
            return 'api-cert.payeezy.com';
        }
    }

}
