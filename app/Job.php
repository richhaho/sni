<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;


/**
 * App\Job
 *
 * @property int $id
 * @property string $type
 * @property int $client_id
 * @property string|null $number
 * @property string|null $noc_number
 * @property string|null $project_number
 * @property string|null $status
 * @property string|null $name
 * @property string|null $address_source
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $address_corner
 * @property string|null $city
 * @property string|null $county
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $last_day
 * @property float $contract_amount
 * @property string|null $default_materials
 * @property string|null $legal_description
 * @property string|null $folio_number
 * @property float $unpaid_balance
 * @property float $interest_charged
 * @property float $interest_asof_date
 * @property float $interest_rate
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $private_type
 * @property int $is_mall_unit
 * @property int $is_tenant
 * @property int $is_condo
 * @property string|null $association_name
 * @property string|null $a_unit_number
 * @property string|null $mall_name
 * @property string|null $m_unit_number
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Attachment[] $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\JobChangeOrder[] $changes
 * @property-read \App\Client $client
 * @property-read mixed $full_address
 * @property-read mixed $full_address_no_country
 * @property-read mixed $search_string
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Note[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\JobParty[] $parties
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\JobPaymentHistory[] $payments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\WorkOrder[] $workorders
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Job onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job open()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAUnitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAddressCorner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAddressSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereAssociationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereContractAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereDefaultMaterials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereFolioNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereInterestAsofDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereInterestCharged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereIsCondo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereIsMallUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereIsTenant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereLastDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereLegalDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereMUnitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereMallName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereNocNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job wherePrivateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereProjectNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereUnpaidBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Job whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Job withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Job withoutTrashed()
 * @mixin \Eloquent
 */
class Job extends Model
{
    //
    use Searchable;
    use SoftDeletes;
    protected $dates = ['started_at','last_day', 'research_complete', 'research_start'];
    protected $fillable = ['type','client_id','number', 'project_number','noc_number',
        'name','address_source','address_1','address_2','address_corner','city',
        'county','state','zip', 'country', 'started_at','last_day','status', 
        'contract_amount','interest_rate','default_materials','legal_description', 'legal_description_source',
        'folio_number','private_type','is_mall_unit','is_tenant','is_condo',
        'association_name','a_unit_number','mall_name','m_unit_number','coordinate_id', 'research_complete', 'research_start', 'notify_email'];

    protected $appends = ['full_address'];
        
    public function client() {
        return $this->belongsTo('App\Client')->withTrashed();;
    }
    

    
    public function parties()
    {
        return $this->hasMany('App\JobParty');
    }
    
     public function workorders()
    {
        return $this->hasMany('App\WorkOrder')->withTrashed();
    }

    public function firstWorkorder()
    {
        return $this->workorders()->where('type', 'notice-to-owner')->where(function($q) {
            $q->where('service', null)->orwhere('service', 'full');
        })->where('deleted_at', null)->where('status','open')->first();
    }
    
    
    public function attachments()
    {
        return $this->morphMany('App\Attachment', 'attachable');
    }
    
    public function notes()
    {
        return $this->morphMany('App\Note', 'noteable')->withTrashed();
    }
    
    public function scopeOpen($query) {
        return $query->where('status','!=','closed')->orwhereNull('status');
    }
    
    public function getFullAddressAttribute() {
        if (strlen(trim($this->address_1)) == 0 && strlen($this->address_2)==0) {
             $lines[] =($this->address_corner);
             $line2 = array();
             if(strlen($this->city) > 0) {
                $line2[] = $this->city;
            }
            if(strlen($this->state) > 0) {
                $line2[] = strtoupper($this->state);
            }
            if(strlen($this->zip) > 0) {
                $line2[] = $this->zip;
            }
            
            $lines[] = implode(' ',$line2);
            $xaddress = implode('<br>',$lines);
         } else {
            if(strlen($this->address_1) > 0) {
               $lines[] = $this->address_1;
            }
            if(strlen($this->address_2) > 0) {
               $lines[] = $this->address_2;
            }
            $line2 = array();

            if(strlen($this->city) > 0) {
                $line2[] = $this->city;
            }
            if(strlen($this->state) > 0) {
                $line2[] = strtoupper($this->state);
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
            $xaddress = implode('<br>',$lines);
         }
        
        return trim($xaddress);
        
    }
    
        public function getFullAddressNoCountryAttribute() {
            
         if (strlen($this->address_1) == 0 && strlen($this->address_2) == 0) {
             $lines[] =($this->address_corner);
              $line2 = array();
              if(strlen($this->city) > 0) {
                $line2[] = $this->city;
            }
            if(strlen($this->state) > 0) {
                $line2[] = strtoupper($this->state);
            }
            if(strlen($this->zip) > 0) {
                $line2[] = $this->zip;
            }
            
            $lines[] = implode(' ',$line2);
            $xaddress = implode('<br>',$lines);
         } else {
            if(strlen($this->address_1) > 0) {
               $lines[] = $this->address_1;
            }
            if(strlen($this->address_2) > 0) {
               $lines[] = $this->address_2;
            }
            
             if(strlen($this->address_corner) > 0) {
                $lines[] = $this->address_corner;
             }
            $line2 = array();
            if(strlen($this->city) > 0) {
                $line2[] = $this->city;
            }
            if(strlen($this->state) > 0) {
                $line2[] = strtoupper($this->state);
            }
            if(strlen($this->zip) > 0) {
                $line2[] = $this->zip;
            }
            $lines[] = implode(' ',$line2);

            $xaddress = implode('<br>',$lines);
         }
        
        return trim($xaddress);
        
    }

    public function getLegalDescriptionAttribute($value) {
        return str_replace(chr(13),"",$value);
    }

    
    public function getSearchStringAttribute() {
        return str_replace('<br>', ' - ', $this->full_address);
    }
    
    public function payments()
    {
        return $this->hasMany('App\JobPaymentHistory');
    }
    public function coordinate()
    {
        return Coordinate::where('id', $this->coordinate_id)->first();
    }
    
    public function changes()
    {
        return $this->hasMany('App\JobChangeOrder');
    }
    
    public function toSearchableArray()
    {
        $array = $this->toArray();
        unset($array['id']);
        unset($array['started_at']);
        unset($array['contract_amount']);
        unset($array['unpaid_balance']);
        unset($array['interest_charged']);
        unset($array['interest_asof_date']);
        unset($array['interest_rate']);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        unset($array['full_address']);
        unset($array['name_entity_name']);
        unset($array['status']);
        unset($array['type']);
        unset($array['address_source']);
        unset($array['client_id']);
        unset($array['private_type']);
        unset($array['association_name']);
        unset($array['a_unit_number']);
        unset($array['mall_name']);
        unset($array['m_unit_number']);
        return $array;
    }

    public static function scopeSearchByKeyword($query, $keyword)
    {
        if ($keyword!='') {
            $query->where(function ($query) use ($keyword) {
                $query->where("name", "LIKE","%$keyword%")
                    ->orWhere("address_source", "LIKE", "%$keyword%")
                    ->orWhere("address_corner", "LIKE", "%$keyword%")
                    ->orWhere("address_1", "LIKE", "%$keyword%")
                    ->orWhere("address_2", "LIKE", "%$keyword%")
                    ->orWhere("state", "LIKE", "%$keyword%")
                    ->orWhere("city", "LIKE", "%$keyword%")
                    ->orWhere("county", "LIKE", "%$keyword%")
                    ->orWhere("country", "LIKE", "%$keyword%")
                    ->orWhere("default_materials", "LIKE", "%$keyword%")
                    ->orWhere("legal_description", "LIKE", "%$keyword%");
            });
        }
        return $query;
    }
    
    public function getChanges($data)
    {
        $fields = ['type','client_id','number', 'project_number','noc_number',
        'name','address_source','address_1','address_2','address_corner','city',
        'county','state','zip', 'country', 'started_at','last_day','status', 
        'contract_amount','interest_rate','default_materials','legal_description',
        'folio_number','private_type','is_mall_unit','is_tenant','is_condo',
        'association_name','a_unit_number','mall_name','m_unit_number','coordinate_id', 'research_complete', 'research_start'];
        $thisArray = $this->toArray();

        $changes = array();
        foreach($fields as $field) {
            if (!isset($data[$field])) continue;
            if ($data[$field] != $thisArray[$field]) {
                if (($field == 'started_at' || $field == 'last_day') && $thisArray[$field]) {
                    if (substr($thisArray[$field], 0, 10) == $data[$field]) continue;
                }
                $change['field'] = $field;
                $change['old'] = $thisArray[$field];
                $change['new'] = $data[$field];
                $changes[]= $change;
            }
        }
        return $changes;
    }

    public function reminders() {
        return JobReminders::where('job_id', $this->id)->where('deleted_at', null);
    }

    public function nocs() {
        if ($this->noc_number) {
            $noc = JobNoc::where('job_id', $this->id)->where('noc_number', $this->noc_number)->first();
            if (empty($noc)) {
                $noc = JobNoc::create([
                    'job_id' => $this->id,
                    'noc_number' => $this->noc_number,
                    'recorded_at' => date('Y-m-d H:i:s'),
                    'expired_at' => date('Y-m-d H:i:s', strtotime('+1 year'))
                ]);
            }
        }
        return JobNoc::where('job_id', $this->id);
    }
    
    public function balance() {
        $balance = $this->contract_amount;
        foreach($this->changes as $change) {
            $balance+=$change->amount;
        }
        foreach($this->payments as $pay) {
            $balance=$balance - $pay->amount;
        }
        return $balance;
    }
    
    public function linked_jobs() {
        return Job::where('linked_to', $this->id)->where('deleted_at', null);
    }

    public function sharedUsers()
    {
        $userIds = SharedJobToUser::where('job_id', $this->id)->get()->pluck('user_id')->toArray();
        return User::whereIn('id', $userIds)->get();
    }

    public function sharedUsersName()
    {
        return implode(', ', $this->sharedUsers()->pluck('full_name')->toArray());
    }

}
