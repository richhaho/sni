<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Carbon\Carbon;


/**
 * App\Client
 *
 * @property int $id
 * @property string|null $company_name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $city
 * @property string|null $county
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $fax
 * @property string|null $email
 * @property int $parent_client_id
 * @property int $client_user_id
 * @property string|null $default_materials
 * @property float $interest_rate
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $status
 * @property string $gender
 * @property string $billing_type
 * @property string $send_certified
 * @property string $print_method
 * @property string|null $deleted_at
 * @property string|null $title
 * @property string|null $payeezy_type
 * @property string|null $payeezy_value
 * @property string|null $payeezy_cardholder_name
 * @property string|null $payeezy_exp_date
 * @property mixed|null $signature
 * @property-read \App\User $admin_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ContactInfo[] $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entity[] $entities
 * @property-read mixed $address_no_country
 * @property-read mixed $full_address
 * @property-read mixed $full_name
 * @property-read mixed $mailing_address
 * @property-read mixed $search_string
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Job[] $jobs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $open_invoices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\WorkOrder[] $work_orders
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client enable()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Client onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereBillingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereClientUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereDefaultMaterials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereParentClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePayeezyCardholderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePayeezyExpDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePayeezyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePayeezyValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client wherePrintMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereSendCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Client withoutTrashed()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use Searchable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_name','title', 'first_name','last_name', 'address_1','address_2','county','city','state','zip', 'country', 'phone', 'mobile','fax','email','parent_client_id','print_method','default_materials','interest_rate','status','gender','billing_type','send_certified','signature', 'service', 'subscription', 'expiration', 'self_30day_rate', 'self_365day_rate', 'full_30day_rate', 'full_365day_rate', 'description', 'monthly_payment', 'montly_recurring_price'];
    protected $appends = ['search_string'];
    
    
    
    public function getFullNameAttribute() {
        $xatrribute = trim($this->first_name . ' ' . $this->last_name);
        if (trim($xatrribute) =='') {
            return $this->company_name;
        }   else {
            return $xatrribute;
        }     
        
    }

    public function getSubscriptionRateAttribute() {
        if ($this->service == 'full' && $this->subscription == '30') {
            return $this->full_30day_rate;
        } elseif  ($this->service == 'full' && $this->subscription == '365') {
            return $this->full_365day_rate;
        } elseif  ($this->service == 'self' && $this->subscription == '30') {
            return $this->self_30day_rate;
        } elseif  ($this->service == 'self' && $this->subscription == '365') {
            return $this->self_365day_rate;
        } else {
            return null;
        }
    }
    
     public function jobs()
    {
        return $this->hasMany('App\Job')->withTrashed();;
    }
    
    public function admin_user() {
        return $this->belongsTo('App\User','client_user_id')->withTrashed();
    }
    
    public function users() {
        return $this->hasMany('App\User')->isRole(['client','client-secondary'])->withTrashed();
    }
    public function activeusers() {
        return $this->hasMany('App\User')->isRole(['client','client-secondary']);
    }
    
    public function entities() {
        return $this->hasMany('App\Entity')->withTrashed();;
    }
    
    public function invoices() {
        return $this->hasMany('App\Invoice');
    }
    public function batch_invoices() {
        return $this->hasMany('App\InvoiceBatches');
    }
    
    public function open_invoices() {
        return $this->hasMany('App\Invoice')->where('status','open')->where('created_at', '<=' , Carbon::now()) ;
    }
    
    public function contacts()
    {
        return $this->hasManyThrough('App\ContactInfo', 'App\Entity')->withTrashed();
    }
    
    public function work_orders()
    {
        return $this->hasManyThrough('App\WorkOrder', 'App\Job')->withTrashed();
    }
    
     
    
    public function getSearchStringAttribute() {
         $lines = array();
         $line2 = array();
         if(strlen($this->address_1) > 0) {
            $lines[] = $this->address_1;
         }
         if(strlen($this->address_2) > 0) {
            $lines[] = $this->address_2;
         }
         
         if(strlen($this->city) > 0) {
             $line2[] = $this->city;
         }
         if(strlen($this->state) > 0) {
             $line2[] = strtoupper($this->state);
         }
         if(strlen($this->zip) > 0) {
             $line2[] = $this->zip;
         }
         $lines[] = implode(', ',$line2);
         if(strlen($this->country) > 0) {
            $lines[] = $this->country;
         }   
         $xaddress = implode(' - ',$lines);
         
        
        return trim($xaddress);
        
    }
    
    public function toSearchableArray()
    {
        $array = $this->toArray();
       
        unset($array['title']);
        unset($array['primary']);
        unset($array['parent_client_id']);
        unset($array['client_user_id']);
        unset($array['interest_rate']);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        unset($array['status']);
        unset($array['gender']);
        unset($array['billing_type']);
        unset($array['send_certified']);
        unset($array['print_method']);
        unset($array['full_name']);
        unset($array['search_string']);    
        unset($array['payeezy_type']);    
        unset($array['payeezy_value']); 
        unset($array['payeezy_cardholder_name']); 
        unset($array['payeezy_exp_date']); 
        
        return $array;
    }
    
    
    public function scopeEnable($query)
    {
        return $query->where('status', 4);
    }
    
    
      public function getFullAddressAttribute() {
         $lines = array();
         $line2 = array();
         if(strlen($this->address_1) > 0) {
            $lines[] = $this->address_1;
         }
         if(strlen($this->address_2) > 0) {
            $lines[] = $this->address_2;
         }
         
         if(strlen($this->city) > 0) {
             $line2[] = $this->city;
         }
         if(strlen($this->state) > 0) {
             $line2[] = $this->state;
         }
         if(strlen($this->zip) > 0) {
             $line2[] = $this->zip;
         }
         $lines[] = implode(' ',$line2);
         if(strlen($this->country) > 0) {
            if (strtoupper($this->country) =='UNITED STATES') {
                
            }else {
                $lines[] = $this->country;
            }
         }   
         $xaddress = implode('<br />',$lines);
         
        
        return trim($xaddress);
        
    }
    
    
     public function getAddressNoCountryAttribute() {
         $lines = array();
         $line2 = array();
         if(strlen($this->address_1) > 0) {
            $lines[] = $this->address_1;
         }
         if(strlen($this->address_2) > 0) {
            $lines[] = $this->address_2;
         }
         
         if(strlen($this->city) > 0) {
             $line2[] = $this->city;
         }
         if(strlen($this->state) > 0) {
             $line2[] = $this->state;
         }
         if(strlen($this->zip) > 0) {
             $line2[] = $this->zip;
         }
         $lines[] = implode(' ',$line2);
       
         $xaddress = implode('<br />',$lines);
         
        
        return trim($xaddress);
        
    }
    
    public function getMailingAddressAttribute() {
        return $this->company_name . '<br>' . $this->address_no_country;
    }
    
}
