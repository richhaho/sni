<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SharedJobToUser;
use App\User;
use App\TempUser;
use App\Job;
use App\ContractTracker;
use App\JobNoc;
use App\JobLog;
use App\JobParty;
use App\ContactInfo;
use App\Entity;
use App\WorkOrderType;
use App\WorkOrder;
use App\Client;
use App\Coordinate;
use Session; 
use DB;
use Auth;
use App\AttachmentType;
use App\Attachment;
use App\JobAddressSearchAPI;
use App\Note;
use Carbon\Carbon;

use Response;
use Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAttachment;
use App\Notifications\ShareJobToMornitoringUser;
use DateTime;
//composer require guzzlehttp/guzzle:~6.0
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as Http_Client;
use App\PropertyRecords;
class JobsController extends Controller
{
    
     private $wo_types;
     
     private $statuses = [ 
            'cancelled' => 'Cancelled',
            'cancelled charge' => 'Cancelled Charge',
            'cancelled duplicate' => 'Cancelled Duplicate',
            'cancelled duplicate needs credit' => 'Cancelled Duplicate Needs Credit',
            'cancelled no charge' => 'Cancelled No Charge',
            'closed' => 'Closed',
            'completed' => 'Completed',
            'data entry' => 'Data Entry',
            'edit' => 'Edit',
            'open' => 'Open',
            'payment pending' => 'Payment Pending',
            'pending' => 'Pending',
            'pending client' => 'Pending Client',
            'phone calls' => 'Phone Calls',
            'print' => 'Print',
            'qc' => 'Q/C',
            'search' => 'Search',
            'tax rolls' => 'Tax Rolls',
            'atids' => 'Title Search',
         ];
      private  $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
            
        ];
      private $counties=['ALACHUA','BAKER','BAY','BRADFORD','BREVARD','BROWARD','CALHOUN','CHARLOTTE','CITRUS','CLAY','COLLIER','COLUMBIA','DESOTO','DIXIE','DUVAL','ESCAMBIA','FLAGLER','FRANKLIN','GADSDEN','GILCHRIST','GLADES','GULF','HAMILTON','HARDEE','HENDRY','HERNANDO','HIGHLANDS','HILLSBOROUGH','HOLMES','INDIAN RIVER','JACKSON','JEFFERSON','LAFAYETTE','LAKE','LEE','LEON','LEVY','LIBERTY','MADISON','MANATEE','MARION','MARTIN','MIAMI-DADE','MONROE','NASSAU','OKALOOSA','OKEECHOBEE','ORANGE','OSCEOLA','PALM BEACH','PASCO','PINELLAS','POLK','PUTNAM','SANTA ROSA','SARASOTA','SEMINOLE','ST. JOHNS','ST. LUCIE','SUMTER','SUWANNEE','TAYLOR','UNION','VOLUSIA','WAKULLA','WALTON','WASHINGTON'];
     
    public function __construct() {
        $this->wo_types = WorkOrderType::all()->pluck('name','slug')->toArray();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       $clients =  Client::enable()->get()->sortBy('company_name')->pluck('company_name', 'id')->prepend('All',0);
       $jobs = Job::query();
       $job_statuses = [
            'none' => 'All Open',
            'null'=>'Blank/Null',
            'closed' => 'Closed'
             
       ];
       $work_types=WorkOrderType::where('deleted_at',null)->pluck('name','slug')->toArray();
       $job_statuses=array_merge($job_statuses,$work_types);


       
       if (session()->has('job_filter.name')) {
          $jobs->where('name','LIKE','%' . session('job_filter.name') .'%');
       }
       
       if (session()->has('job_filter.client')) {
          $jobs->where('client_id',session('job_filter.client'));
       }
       if (session()->has('job_filter.job_type')) {
           if(session('job_filter.job_type') != 'all') {
               $jobs->where('type',session('job_filter.job_type'));
           }
       }
       if (session()->has('job_filter.job_status')) {
           if(session('job_filter.job_status') != 'none') {
               if(session('job_filter.job_status') != 'null'){  
                 $jobs->where('status',session('job_filter.job_status'));
               }else{
                  $jobs->where('status',null)->orwhere('status','');  
               }
                
           } else {
              $jobs->where(function($q) {
                    $q->where('status','!=','closed')->orwhereNull('status');
                });
           }
       } else {
           $jobs->where(function($q) {
                    $q->where('status','!=','closed')->orwhereNull('status');
                });
       }
       
        if (session()->has('job_filter.daterange')) {
            if(session('job_filter.daterange') != '') {
                $date_range=session('job_filter.daterange');
                $date_type="/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/";
                
               if (preg_match($date_type,$date_range)){

                $dates = explode(' - ',session('job_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $jobs->where([['started_at','>=',$from],['started_at','<=',$to]])->orderBy('started_at','desc');
                Session::flash('message', null);
                }else{
                    Session::flash('message', 'Input not in expected range format');
                }
            }
        }
       
       
       
       $jobs = $jobs->orderBy('id','DESC')->paginate(15);
       Session::put('backUrl',\URL::full());
       $data = [
           'jobs' => $jobs,
           'clients' => $clients,
           'job_statuses' => $job_statuses
       ];
         
       return view('admin.jobs.index',$data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewLog($id)
    {
        $job=Job::where('id',$id)->first();
        if (empty($job)){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('jobs.index');
        }
        $logs = JobLog::where('job_id', $id);
        if (session()->has('joblog_filter.fullname')) {
            $logs->where('user_name','LIKE','%' . session('joblog_filter.fullname') .'%');
        }
        if (session()->has('joblog_filter.daterange')) {
            if(session('joblog_filter.daterange') != '') {
                $date_range=session('joblog_filter.daterange');
                $date_type="/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/";
                
               if (preg_match($date_type,$date_range)){

                $dates = explode(' - ',session('joblog_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $logs->where([['edited_at','>=',$from],['edited_at','<=',$to]]);
                Session::flash('message', null);
                }else{
                    Session::flash('message', 'Input not in expected range format');
                }
            }
        }
    
        $data = [
            'job' => $job,
            'logs' => $logs->orderBy('edited_at','desc')->paginate(15)
        ];
            
       return view('admin.jobs.log',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       $clients =  Client::enable()->get()->pluck('company_name', 'id')->prepend('', '');;
       
      
       $address_sources =  array("TR","NOC","ATIDS","SubBiz","Other");
       $job_statuses = [
            // 'none' => 'All',
            null=>'Blank/Null',
            'closed' => 'Closed'
             
       ];
       $work_types=WorkOrderType::where('deleted_at',null)->pluck('name','slug')->toArray();
       $job_statuses=array_merge($work_types,$job_statuses);

       
       $job_types = [
           'private' => 'Private - Residential, Commercial properties etc',
           'public' => 'Public - Roadwork, Airport, Government buildings etc',
       ];
       $contract_amount=0;

       $contract_tracker = $request->has('contract_tracker') ? ContractTracker::where('id', $request->contract_tracker)->first() : null;
       if ($contract_tracker && $contract_tracker->is_converted) {
            return redirect()->route('jobs.create');
       }
       
       $data = [
         'clients' => $clients,
         'job_types'=>$job_types,
          'job_statuses' => $job_statuses,
          'address_sources' =>$address_sources,
          'contract_amount' =>$contract_amount,
          'contract_tracker' =>$contract_tracker,
          'counties' => $this->counties
        ];
        return view('admin.jobs.create',$data);
    }

    public function createLog($data, $job) {
        $jobfields = ['type','client_id','number', 'project_number','noc_number',
        'name','address_source','address_1','address_2','address_corner','city',
        'county','state','zip', 'country', 'started_at','last_day','status', 
        'contract_amount','interest_rate','default_materials','legal_description',
        'folio_number','private_type','is_mall_unit','is_tenant','is_condo',
        'association_name','a_unit_number','mall_name','m_unit_number','coordinate_id'];
        $changeArray=array();
        foreach($jobfields as $field) {
            if (isset($data[$field])) {
                $change['field'] = $field;
                $change['old'] = null;
                $change['new'] = $data[$field];
                $changeArray[]= $change;
            }
        }
        $changes = json_encode($changeArray);
        if (count($changeArray)>0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
                'type' => 'created',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'client_id' => 'required',
          'type' => 'required',    
          'name' => 'required',
          'started_at' => 'required|date|date_format:m/d/Y',
          'address_1'=>'required_if:address_corner,""',
          'city'=>'required',
          'county'=>'required',
          'interest_rate'=>'numeric',
          'contract_amount' => 'numeric',
        ]);
         
        $address_sources =  array("TR","NOC","ATIDS","SubBiz","Other");
        $data = $request->all();
        if (strlen($data['started_at']) > 0 ) {
            $data['started_at'] = date('Y-m-d', strtotime($data['started_at']));
        } else {
             $data['started_at'] = NULL;
        }
        if (strlen($data['last_day']) > 0 ) {
            $data['last_day'] = date('Y-m-d', strtotime($data['last_day']));
        } else {
             $data['last_day'] = NULL;
        }
        
        $data['address_source'] = $address_sources[$data['address_source']];
        $job = Job::create($data);
        $this->createLog($data, $job);
        $job->search_status='new';
        $job->save();
        $client = Client::findOrFail($request->client_id);
  
        $contact = $client->contacts->where('primary',2)->first();
        
        if ($contact) {
            
            $job_party =  new JobParty();
            $job_party->job_id = $job->id;
            $job_party->contact_id = $contact->id;
            $job_party->entity_id = $contact->entity_id;
            $job_party->type= "client";
            $job_party->save();
        }


        $owner_name=$request->owner_name;
        $owner_address_1=$request->owner_address_1;
        $owner_address_2=$request->owner_address_2;
        $owner_city=$request->owner_city;
        $owner_state=$request->owner_state;
        $owner_zip=$request->owner_zip;


        //=== pull landowner from owner data on property_records 
        if($owner_name){
          $contacts=$client->contacts;
          $matched=false;
          foreach ($contacts as $contact) {

              $entity_contact=Entity::where('id',$contact->entity_id)->first();
              $first_name='';
              $last_name='';

              if ($entity_contact->firm_name==strtoupper($owner_name) && $contact->first_name==$first_name && $contact->last_name==$last_name && (substr($contact->address_1,0,5)==strtoupper(substr($owner_address_1,0,5)) || $contact->address_1==$owner_address_1) && $contact->city==strtoupper($owner_city) && $contact->zip==$owner_zip){

                  $entity_contact->latest_type='owner';
                  $entity_contact->save();

                  $data['entity_id'] = $entity_contact->id;
                  $data['contact_id'] = $contact->id;
                  $data['type'] = 'landowner';
                  $data['job_id'] = $job->id;
                  $data['source'] = 'OTHR';
                  $landowner_deed_number='';
                  $data['landowner_deed_number']=$landowner_deed_number;
                  $newJobParty = JobParty::create($data);

                  $matched=true;   
              }
          }

          if (!$matched){

              $data['firm_name']=strtoupper($owner_name);
              $data['latest_type']='owner';
              $data['client_id']=$job->client_id;
              $data['is_hot']=0;
              $data['hot_id']=0;

              $entity = Entity::create($data);

              $xdata['first_name'] = '';
              $xdata['last_name'] = '';
              $xdata['gender']='none';
              $xdata['address_1'] = strtoupper($owner_address_1);
              $xdata['address_2'] = strtoupper($owner_address_2);
              $xdata['city'] = strtoupper($owner_city);
              $xdata['state'] = strtoupper($owner_state);
              $xdata['zip'] = $owner_zip;
              $xdata['country'] = 'USA';
              if (strlen($owner_state)>2){
                  $xdata['country'] = strtoupper($owner_state);
                  $xdata['state']='';
              }

              $new_contact = ContactInfo::create($xdata);
              $new_contact->entity_id = $entity->id;
              $new_contact->primary = 1;
              $new_contact->save();

              $xdata['entity_id'] = $entity->id;
              $xdata['contact_id'] = $new_contact->id;
              $xdata['type'] = 'landowner';
              $xdata['job_id'] = $job->id;

              $landowner_deed_number='';
              $xdata['landowner_deed_number']=$landowner_deed_number;
              $newJobParty = JobParty::create($xdata);

          }
        }
        ////////////////////////////////////////////////////

        $createdFrom = '';
        if ($request->has('contract_tracker')) {
            $createdFrom = ' from contract tracker.';
            $contract_tracker = ContractTracker::where('id', $request->contract_tracker)->first();
            if ($contract_tracker && !$contract_tracker->is_converted) {
                $contract_tracker->is_converted = 1;
                $contract_tracker->save();
                $job->contract_tracker_id = $contract_tracker->id;
                $job->save();
                if ($contract_tracker->contract_file && Storage::disk()->exists($contract_tracker->contract_file)) {
                    $attachment = new Attachment();
                    $file = Storage::get($contract_tracker->contract_file);
                    
                    $attachment->type = 'contract-tracker';
                    $attachment->description = 'Attached from contract tracker';
                    $attachment->original_name = $contract_tracker->file_original_name;
                    $attachment->file_mime = $contract_tracker->file_mime;
                    $attachment->file_size = $contract_tracker->file_size;
                    $attachment->user_id = Auth::user()->id;
                    $job->attachments()->save($attachment);
                    $attachment->save();
                    
                    $xfilename = "attachment-" .$attachment->id . "." . $contract_tracker->file_extension;
                    $xpath = 'attachments/jobs/' . $job->id . '/';
                    Storage::put($xpath.$xfilename, $file);
                    $attachment->file_path = $xpath .  $xfilename;
                    $attachment->save();
                    
                    switch ($contract_tracker->file_mime) {
                        case 'application/pdf':
                            $xblob = $file;
                            $img = new \Imagick();
                            $img->readImageBlob($xblob);
                            $img->setIteratorIndex(0);
                            $img->setImageFormat('png');
                            $img->setbackgroundcolor('rgb(64, 64, 64)');
                            $img->thumbnailImage(300, 300, true, true);
                            Storage::put($xpath . "thumbnail-" .$attachment->id . ".png",$img);
                            $attachment->thumb_path = $xpath . "thumbnail-" .$attachment->id . ".png";
                        
                            break;
                        case 'image/jpeg':
                        case 'image/png':
                            $xblob = $file;
                            $img = new \Imagick();
                            $img->readImageBlob($xblob);
                            $img->setImageFormat('png');
                            $img->setbackgroundcolor('rgb(64, 64, 64)');
                            $img->thumbnailImage(300, 300, true, true);
                            Storage::put($xpath . "thumbnail-" .$attachment->id . ".png",$img);
                            $attachment->thumb_path = $xpath . "thumbnail-" .$attachment->id . ".png";
                            break;
                        default:
                            $attachment->thumb_path = null;
                            break;
                    }

                    $attachment->clientviewable='yes';
                    $attachment->save();
                }
            }
        }

        Session::flash('message', 'Job ' .$job->name . ' created'. $createdFrom);
        //return redirect()->to(($request->input('redirects_to')));
        return redirect()->route('parties.index',$job->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $job = Job::findOrFail($id);
       
       $job_types = [
           'public' => 'Public - Roadwork, Airport, Government buildings etc',
           'private' => 'Private - Residential, Commercial properties etc',
       ];
        $attachment_types= AttachmentType::get()->pluck('name','slug');
         
         $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
            
        ];
        $data = [
         'parties_type' =>$parties_type,
          'job_types'=>$job_types,
          'job' =>$job,
          'attachment_types' => $attachment_types
         ];
        return view('admin.jobs.show',$data);
    }

    /**
     * Show summary
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function summary($job_id)
    {
        $job=Job::where('id',$job_id)->first();
        if (empty($job)){
            Session::flash('message', 'Job ' .$job_id . ' has been deleted already.');
            return redirect()->back();
        }
        $work_orders=WorkOrder::where('job_id',$job_id)->where('status', '!=', 'temporary')->get();
        $nto_printed_at = '';
        $nto_filled_timly = 'No';
        foreach($work_orders as $work) {
            $attachments = $work->attachments()->where('description', 'like', 'AUTOMATICALLY GENERATED NOTICE TO OWNER%')->get();
            foreach($attachments as $attachment) {
                if ($attachment->printed_at && $attachment->printed_at > $nto_printed_at) {
                    $nto_printed_at = $attachment->printed_at;
                }
                if ($nto_filled_timly == 'Yes') continue;
                $diff = date_diff(date_create($job->started_at), date_create($attachment->created_at));
                $nto_filled_timly = $diff->format('%a') > 40 ? 'No' : 'Yes';
            }
        }

        $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
        ];

        $mailing_types = [
            'none' => 'None',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',   
            'certified-nongreen' => 'Certfied Non Green', 
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];

        $data = [ 
          'job' =>$job,
          'work_orders'=>$work_orders,
          'nto_printed_at'=> $nto_printed_at ? date('m/d/Y', strtotime($nto_printed_at)): '',
          'nto_filled_timly'=> $nto_filled_timly,
          'wo_types' => $this->wo_types,
          'parties_type' => $parties_type,
          'mailing_types' => $mailing_types
        ];
        return view('admin.jobs.summary',$data);
    }
    
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('jobs.index');
        }

        $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
        ];
        
        if (session()->has('note')) {
            $xnote = session('note');
        } else {
            $xnote = "";
        }
        
        if (session()->has('payment')) {
            $xpayment = session('payment');
        } else {
            $xpayment = "";
        }
        
        if (session()->has('change')) {
            $xchange = session('change');
        } else {
            $xchange = "";
        }

        $markStatuses = [
            'edit' => 'Edit',
            'tax rolls' => 'Tax rolls',
            'phone calls' => 'Phone calls'
        ];
        
        
        $job = Job::findOrFail($id);
        $clients =  Client::enable()->get()->pluck('company_name', 'id');
        $address_sources =  array("TR","NOC","ATIDS","SubBiz","Other");
        $job_types = [
           'public' => 'Public - Roadwork, Airport, Government buildings etc',
           'private' => 'Private - Residential, Commercial properties etc',
       ];

        $job_statuses = [
            // 'none' => 'All',
            null=>'Blank/Null',
            'closed' => 'Closed'
             
       ];
       $work_types=WorkOrderType::where('deleted_at',null)->pluck('name','slug')->toArray();
       $job_statuses=array_merge($work_types,$job_statuses);
       
        $attachment_types= AttachmentType::where('slug','!=','generated')->get()->pluck('name','slug');
        $available_notices = [ 
            'amend-claim-of-lien',
            'amended-notice-to-owner',
            'claim-of-lien',
            'conditional-waiver-and-release-of-lien-upon-final-payment',
            'conditional-waiver-and-release-of-lien-upon-progress-payment',
            'contractors-final-payment-affidavit',
            'notice-of-bond',
            'notice-of-commencement',
            'notice-of-termination',
            'notice-of-contest-of-claim-against-payment-bond',
            'notice-of-contest-of-lien',
            'notice-to-owner',
            'notice-of-non-payment',
            'notice-of-nonpayment-for-bonded-private-jobs-statutes-713',
            'notice-of-nonpayment-for-government-jobs-statutes-255',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'out-of-state-nto-preliminary-notice-of-lien-rights',
            'rescission-letter',
            'satisfaction-of-lien',
            'sworn-statement-of-account',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment'
        ];
        $work_orders=WorkOrder::where('job_id',$id)->where('status', '!=', 'temporary')->get();

        
        if (session()->has('job.apiSearch')) {
          $apiSearch_str=session('job.apiSearch');
          $apiSearch_json=json_decode($apiSearch_str);
          $i=-1;
          foreach ($apiSearch_json->properties as $property){
             $i++;
             $property_numbers[$i]=$property->legal->apn_original." : ".$property->legal->legal_description;

          }
          session()->forget('job.apiSearch');
        } else{
          $apiSearch_str="";
          $property_numbers=[];
        }
        $coordinates = Coordinate::where('client_id', $job->client->id)
                      ->where('deleted_at', null)->get()->pluck('full_name', 'id')->prepend('', '');
        $data = [ 
          'clients' => $clients,
          'job_types'=>$job_types,
          'job' =>$job,
          'attachment_types' => $attachment_types,
          'work_order' => request()->input('workorder'),
          'xnote' => $xnote,
          'job_statuses' =>$job_statuses,
          'xpayment' => $xpayment,
          'xchange' => $xchange,
          'wo_types' => ['all' => 'All'] + $this->wo_types,
          'statuses' => ['all' => 'All'] + $this->statuses,
          'available_notices' => $available_notices,
          'address_sources' => $address_sources,
          'parties_type' => $parties_type,
          'work_orders'=>$work_orders,
          'property_numbers'=>$property_numbers,
          'apiSearch_str'=>$apiSearch_str,
          'counties' => $this->counties,
          'markStatuses' => $markStatuses,
          'coordinates' => $coordinates
         ];
        return view('admin.jobs.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
          $this->validate($request, [
            //'client_id' => 'required',
            'type' => 'required',    
            'name' => 'required',
              'city' => 'required',
             'interest_rate' => 'numeric',
             'contract_amount' => 'numeric'
            
        ]);

          
        $address_sources =  array("TR","NOC","ATIDS","SubBiz","Other");
        $data = $request->all();
        $data['address_source'] = $address_sources[$data['address_source']];
        if (strlen($data['started_at']) > 0 ) {
            $data['started_at'] = date('Y-m-d', strtotime($data['started_at']));
        } else {
             $data['started_at'] = NULL;
        }
        if (strlen($data['last_day']) > 0 ) {
            $data['last_day'] = date('Y-m-d', strtotime($data['last_day']));
        } else {
             $data['last_day'] = NULL;
        }
        $job = Job::findOrFail($id);
        if (!$request['client_id']){
            $data['client_id'] = $job->client_id;
        }
        if ($job->status == 'closed' && ($job->last_day != $request->last_day) && $request->last_day) {
            $data['status'] = 'open';
        }
        $changeArray = $job->getChanges($data);
        $changes = json_encode($changeArray);
        $job->update($data);
        if (count($changeArray)>0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
            ]);
        }
        $temp_name = $job->name;

        $this->recalculate_date($job);

        Session::flash('message', 'Job ' .$temp_name . ' updated.');
        if ($request->input('workorder') == '') {
            //return redirect()->to(($request->input('redirects_to')));
            return redirect()->route('jobs.edit',$job->id);
        } else {
            return redirect()->route('workorders.edit',$request->input('workorder'));
        }
    }

    public function save_property(Request $request,$id){
        $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('jobs.index');
        }
        $client=$job->client;
        $data=$request->all();
            if (strpos($job->folio_number,$data['folio_number'])===false){
                 if ($job->folio_number) $data['folio_number']=$job->folio_number.'/'.$data['folio_number'];
            }else {$data['folio_number']=$job->folio_number;}
            if (strpos($job->legal_description,$data['legal_description'])===false){
                if($job->legal_description) $data['legal_description']=$job->legal_description."\n". $data['legal_description'];
            }else{$data['legal_description']=$job->legal_description;}
        $changeArray = $job->getChanges($data);
        $changes = json_encode($changeArray);
        $job->update($data);
        if (count($changeArray)>0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
            ]);
        }

        $owner_name=$request->owner_name;
        $owner_address_1=$request->owner_address_1;
        $owner_address_2=$request->owner_address_2;
        $owner_city=$request->owner_city;
        $owner_state=$request->owner_state;
        $owner_zip=$request->owner_zip;


        //=== pull landowner from owner data on property_records 
        if($owner_name){
          $contacts=$client->contacts;
          $matched=false;
          foreach ($contacts as $contact) {

              $entity_contact=Entity::where('id',$contact->entity_id)->first();
              $first_name='';
              $last_name='';

              if ($entity_contact->firm_name==strtoupper($owner_name) && $contact->first_name==$first_name && $contact->last_name==$last_name && (substr($contact->address_1,0,5)==strtoupper(substr($owner_address_1,0,5)) || $contact->address_1==$owner_address_1) && $contact->city==strtoupper($owner_city) && $contact->zip==$owner_zip){

                  $entity_contact->latest_type='owner';
                  $entity_contact->save();

                  $landowners=$job->parties->where('contact_id',$contact->id)->where('type','landowner');
                  if (count($landowners)==0){
                    $data['entity_id'] = $entity_contact->id;
                    $data['contact_id'] = $contact->id;
                    $data['type'] = 'landowner';
                    $data['job_id'] = $job->id;
                    $data['source'] = 'OTHR';
                    $landowner_deed_number='';
                    $data['landowner_deed_number']=$landowner_deed_number;
                    $newJobParty = JobParty::create($data);
                  }  

                  $matched=true;   
              }
          }

          if (!$matched){ 

              $data['firm_name']=strtoupper($owner_name);
              $data['latest_type']='owner';
              $data['client_id']=$job->client_id;
              $data['is_hot']=0;
              $data['hot_id']=0;

              $entity = Entity::create($data);

              $xdata['first_name'] = '';
              $xdata['last_name'] = '';
              $xdata['gender']='none';
              $xdata['address_1'] = strtoupper($owner_address_1);
              $xdata['address_2'] = strtoupper($owner_address_2);
              $xdata['city'] = strtoupper($owner_city);
              $xdata['state'] = strtoupper($owner_state);
              $xdata['zip'] = $owner_zip;
              $xdata['country'] = 'USA';
              if (strlen($owner_state)>2){
                  $xdata['country'] = strtoupper($owner_state);
                  $xdata['state']='';
              }

              $new_contact = ContactInfo::create($xdata);
              $new_contact->entity_id = $entity->id;
              $new_contact->primary = 1;
              $new_contact->save();

              $xdata['entity_id'] = $entity->id;
              $xdata['contact_id'] = $new_contact->id;
              $xdata['type'] = 'landowner';
              $xdata['job_id'] = $job->id;

              $landowner_deed_number='';
              $xdata['landowner_deed_number']=$landowner_deed_number;
              $newJobParty = JobParty::create($xdata);

          }

        $job->search_status='done';
        $job->save();
        Session::flash('message', 'Property Search Succeeded.');
        return redirect()->route('jobs.edit',$job->id);
        }
        $job->save();
        Session::flash('message', 'Property Search Failed.');
        return redirect()->route('jobs.edit',$job->id);

    }

    public function select_property(Request $request,$id){
      $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('jobs.index');
        }
      $res= json_decode($request['apiSearch_str']);
      $num=$request['property_number'];
      $api_property=$res->properties[$num];

            if (!isset($api_property->owners)){
                $job->search_status='No Address Found';
                $job->save();
                 
                Session::flash('message', 'No Address Found from API.');
                return redirect()->route('jobs.edit',$job->id);

            }

            $owners=$api_property->owners;
            $legal=$api_property->legal;

           
            if (strpos($job->folio_number,$legal->apn_original)===false){
                 if ($job->folio_number) $job->folio_number=$job->folio_number.'/';
                 $job->folio_number=$job->folio_number. $legal->apn_original; 
            }
           

            if (strpos($job->legal_description,$legal->legal_description)===false){
                $job->legal_description=$job->legal_description."\n". $legal->legal_description;
            }

            $job->save();
            
            $estate_addresses=$api_property->addresses;
            if (isset($estate_addresses[0]->zip_code)){
              $populated_zip=$estate_addresses[0]->zip_code;
            }else{
              $populated_zip="";
            }
            if ($job->zip=="" || !$job->zip){
              $job->zip=$populated_zip;
              $job->save();
            }

            $note = New Note();
            $now = Carbon::now();
            $note_text="Matching Address:";
            foreach ($estate_addresses as $estate_address) {
                $note_text=$note_text.$estate_address->formatted_street_address." ".$estate_address->city." ".$estate_address->state." ".$estate_address->zip_code."\n";
            }
            $note->note_text = $note_text;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = Auth::user()->id;
            $note->viewable = 0;
            $note->noteable_type = 'App\Job';
            $note->client_id=$job->client->id;
            $note = $job->notes()->save($note);



            $sales=$api_property->sales;

            $deed_book='';

            $deed_page='';

            $recent_date='1900-00-00';
            foreach ($sales as $sale) {
                if ($sale->date && $sale->date>$recent_date){
                    $recent_date=$sale->date;
                    if ($sale->deed_book) $deed_book=$sale->deed_book;
                    if ($sale->deed_page) $deed_page=$sale->deed_page;
                }
            }

            ////////////////////////////////////////////// 
            for ($i=0;$i<count($owners);$i++){
              
              if ($owners[$i]->address || !$owners[$i]->last_name) continue;
              for ($j=0;$j<count($owners);$j++){
                if($i==$j || !$owners[$j]->address) continue;
                if($owners[$i]->last_name==$owners[$j]->last_name){
                  $owners[$i]->address=$owners[$j]->address;
                  $owners[$i]->address2=$owners[$j]->address2;
                  $owners[$i]->city=$owners[$j]->city;
                  $owners[$i]->state=$owners[$j]->state;
                  $owners[$i]->zip_code=$owners[$j]->zip_code;
                  break;
                }
              }
            }

            for ($i=0;$i<count($owners);$i++){
              if (!$owners[$i]->address) continue;
              for ($j=$i+1;$j<count($owners);$j++){
                if($owners[$j]->name=='' || !$owners[$j]->address) continue;
                if ($owners[$i]->address==$owners[$j]->address){
                  $owners[$i]->name=$owners[$i]->name.' AND '.$owners[$j]->name;
                  $owners[$j]->name='';
                  $owners[$i]->first_name='';
                  $owners[$i]->last_name='';
                }
              }
            }
            ///////////////////////////////////////////////////


            $contacts=$job->client->contacts;
            $lo=0;
            foreach ($owners as $owner) {

                if ($owner->ended_at || $owner->name=='') continue;
                $lo++;if($lo>10) break;  
                $matched=false;

                foreach ($contacts as $contact) {

                    $entity_contact=Entity::where('id',$contact->entity_id)->first();


                    $first_name=strtoupper($owner->first_name);
                    $last_name=strtoupper($owner->last_name);
                    if (!$first_name) $first_name='';
                    if (!$last_name) $last_name='';

                    if ($entity_contact->firm_name==strtoupper($owner->name) && $contact->first_name==$first_name && $contact->last_name==$last_name && (substr($contact->address_1,0,5)==strtoupper(substr($owner->address,0,5)) || $contact->address_1==$owner->address) && $contact->city==strtoupper($owner->city) && $contact->zip==$owner->zip_code){

                           $entity_contact->latest_type='owner';

                           $entity_contact->save();

                           $landowners=$job->parties->where('contact_id',$contact->id)->where('type','landowner');

                           if (count($landowners)>0){

                                foreach ($landowners as $landowner)

                                {
                                    $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";
                                    $landowner->landowner_deed_number=$landowner_deed_number;

                                }

                                

                           }else{

                                $data['entity_id'] = $entity_contact->id;

                                $data['contact_id'] = $contact->id;

                                $data['type'] = 'landowner';

                                $data['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $data['landowner_deed_number']=$landowner_deed_number.' Date:'.$recent_date;

                                $newJobParty = JobParty::create($data);

                           }

                        $matched=true;   

                        break;

                    }

                }

                if (!$matched){

                    $data['firm_name']=strtoupper($owner->name);

                    $data['latest_type']='owner';

                    $data['client_id']=$job->client_id;

                    $data['is_hot']=0;

                    $data['hot_id']=0;



                    $entity = Entity::create($data);



                    $xdata['first_name'] = strtoupper($owner->first_name);

                    $xdata['last_name'] = strtoupper($owner->last_name);

                    if (!$owner->first_name) $xdata['first_name'] ='';
                    if (!$owner->last_name) $xdata['last_name'] ='';
                    if (isset($owner->gender)) $xdata['gender'] = $owner->gender; else $xdata['gender']='none';

                    $xdata['address_1'] = strtoupper($owner->address);

                    $xdata['address_2'] = strtoupper($owner->address2);

                    $xdata['city'] = strtoupper($owner->city);

                    $xdata['state'] = strtoupper($owner->state);

                    $xdata['zip'] = $owner->zip_code;

                    //$xdata['phone'] = $owner->phone;
                    $xdata['country'] = 'USA';



                    $new_contact = ContactInfo::create($xdata);

                    $new_contact->entity_id = $entity->id;

                    $new_contact->primary = 1;

                    $new_contact->save();



                                $xdata['entity_id'] = $entity->id;

                                $xdata['contact_id'] = $new_contact->id;

                                $xdata['type'] = 'landowner';

                                $xdata['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $xdata['landowner_deed_number']=$landowner_deed_number;

                                $newJobParty = JobParty::create($xdata);

                }

            }

            $job->search_status='done';
            $job->save();
        Session::flash('message', 'Re-run Search succeeded.');
        return redirect()->route('jobs.edit',$job->id);
    }

    public function runsearch($id)
    {
        $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('jobs.index');
        }
            $address=$job->address_1.' '.$job->address_2;

            $city=$job->city;

            $state=$job->state;

            $zipcode=$job->zip;

        $search_api=JobAddressSearchAPI::where('job_id',$id)->first();

        if (count($search_api)>0){
          $address_sent=json_decode($search_api->address_sent);
          $json_response=json_decode($search_api->json_response);
          if ($address_sent->address==$address && $address_sent->city==$city && $address_sent->state==$state && $address_sent->zipcode==$zipcode){
            
            if (count($json_response->properties)>1){
              session(['job.apiSearch' => $search_api->json_response]);
              return redirect()->route('jobs.edit',$job->id);
            }
          }
        }

        $token=env('ESTATED_API_TOKEN');
        // $token='fa1zRLG4WkyZMy05vyxal2Pz0DCwFD';
        // $token='YjgnGy2ZVTtzwR4Sk9YhhW3CGYqs3B';


            $res=null;
            $url_con="https://api.estated.com/property/v3?token=$token&address=$address&city=$city&state=$state&zipcode=$zipcode";
            $http = new Http_Client();

            $response = $http->get($url_con);
             
            $response_data = $response->getBody(); 
            $res=json_decode($response_data);
             
            // $username='viktor1987923@gmail.com';
            // $password='r920216213';
            // $chc = curl_init();
            
            // curl_setopt($chc, CURLOPT_URL, $url_con);
            // curl_setopt($chc, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($chc, CURLOPT_SSL_VERIFYPEER, 0);
            // curl_setopt($chc, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($chc, CURLOPT_VERBOSE, 1);
            // curl_setopt($chc, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            // curl_setopt($chc, CURLOPT_USERPWD, "$username:$password");
            
            // $res  = json_decode(curl_exec($chc));
            // $code=curl_getinfo($chc);

            // curl_close($chc);
             
            //return json_encode($res->properties);


            if (!isset($res->properties)){
                $job->search_status='No Address Found';
                $job->save();
                
                Session::flash('message', 'No Address Found from API.');
                return redirect()->route('jobs.edit',$job->id);

            }
            if (count($res->properties)>1){
              if (count($search_api)>0){
                $search_api->json_response=$response_data;
                $address_sent='{"address":'.'"'.$address.'","city":'.'"'.$city.'","state":'.'"'.$state.'","zipcode":'.'"'.$zipcode.'"}';
                $search_api->address_sent=$address_sent;
                $search_api->save();
              }else{
                $search_api=JobAddressSearchAPI::Create();
                $search_api->job_id=$job->id;
                $address_sent='{"address":'.'"'.$address.'","city":'.'"'.$city.'","state":'.'"'.$state.'","zipcode":'.'"'.$zipcode.'"}';
                $search_api->address_sent=$address_sent;
                $search_api->json_response=$response_data;
                $search_api->save();
              }
              session(['job.apiSearch' => $response_data]);
              return redirect()->route('jobs.edit',$job->id);
            }

            $api_property=$res->properties[0];

            if (!isset($api_property->owners)){
                $job->search_status='No Address Found';
                $job->save();
                 
                Session::flash('message', 'No Address Found from API.');
                return redirect()->route('jobs.edit',$job->id);

            }



            $owners=$api_property->owners;
            $legal=$api_property->legal;

           
            if (strpos($job->folio_number,$legal->apn_original)===false){
                 if ($job->folio_number) $job->folio_number=$job->folio_number.'/';
                 $job->folio_number=$job->folio_number. $legal->apn_original; 
            }
           

            if (strpos($job->legal_description,$legal->legal_description)===false){
                $job->legal_description=$job->legal_description."\n". $legal->legal_description;
            }

            $job->save();
            
            $estate_addresses=$api_property->addresses;
            if (isset($estate_addresses[0]->zip_code)){
              $populated_zip=$estate_addresses[0]->zip_code;
            }else{
              $populated_zip="";
            }
            if ($job->zip=="" || !$job->zip){
              $job->zip=$populated_zip;
              $job->save();
            }
            
            $note = New Note();
            $now = Carbon::now();
            $note_text="Matching Address:";
            foreach ($estate_addresses as $estate_address) {
                $note_text=$note_text.$estate_address->formatted_street_address." ".$estate_address->city." ".$estate_address->state." ".$estate_address->zip_code."\n";
            }
            $note->note_text = $note_text;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = Auth::user()->id;
            $note->viewable = 0;
            $note->noteable_type = 'App\Job';
            $note->client_id=$job->client->id;
            $note = $job->notes()->save($note);



            $sales=$api_property->sales;

            $deed_book='';

            $deed_page='';

            $recent_date='1900-00-00';
            foreach ($sales as $sale) {
                if ($sale->date && $sale->date>$recent_date){
                    $recent_date=$sale->date;
                    if ($sale->deed_book) $deed_book=$sale->deed_book;
                    if ($sale->deed_page) $deed_page=$sale->deed_page;
                }
            }

            ////////////////////////////////////////////// 
            for ($i=0;$i<count($owners);$i++){
              
              if ($owners[$i]->address || !$owners[$i]->last_name) continue;
              for ($j=0;$j<count($owners);$j++){
                if($i==$j || !$owners[$j]->address) continue;
                if($owners[$i]->last_name==$owners[$j]->last_name){
                  $owners[$i]->address=$owners[$j]->address;
                  $owners[$i]->address2=$owners[$j]->address2;
                  $owners[$i]->city=$owners[$j]->city;
                  $owners[$i]->state=$owners[$j]->state;
                  $owners[$i]->zip_code=$owners[$j]->zip_code;
                  break;
                }
              }
            }

            for ($i=0;$i<count($owners);$i++){
              if (!$owners[$i]->address) continue;
              for ($j=$i+1;$j<count($owners);$j++){
                if($owners[$j]->name=='' || !$owners[$j]->address) continue;
                if ($owners[$i]->address==$owners[$j]->address){
                  $owners[$i]->name=$owners[$i]->name.' AND '.$owners[$j]->name;
                  $owners[$j]->name='';
                  $owners[$i]->first_name='';
                  $owners[$i]->last_name='';
                }
              }
            }
            ///////////////////////////////////////////////////



            $contacts=$job->client->contacts;
            $lo=0;
            foreach ($owners as $owner) {

                if ($owner->ended_at || $owner->name=='') continue;
                $lo++;if($lo>10) break; 
                $matched=false;

                foreach ($contacts as $contact) {

                    $entity_contact=Entity::where('id',$contact->entity_id)->first();


                    $first_name=strtoupper($owner->first_name);
                    $last_name=strtoupper($owner->last_name);
                    if (!$first_name) $first_name='';
                    if (!$last_name) $last_name='';

                    if ($entity_contact->firm_name==strtoupper($owner->name) && $contact->first_name==$first_name && $contact->last_name==$last_name && (substr($contact->address_1,0,5)==strtoupper(substr($owner->address,0,5)) || $contact->address_1==$owner->address) && $contact->city==strtoupper($owner->city) && $contact->zip==$owner->zip_code){

                           $entity_contact->latest_type='owner';

                           $entity_contact->save();

                           $landowners=$job->parties->where('contact_id',$contact->id)->where('type','landowner');

                           if (count($landowners)>0){

                                foreach ($landowners as $landowner)

                                {
                                    $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";
                                    $landowner->landowner_deed_number=$landowner_deed_number;

                                }

                                

                           }else{

                                $data['entity_id'] = $entity_contact->id;

                                $data['contact_id'] = $contact->id;

                                $data['type'] = 'landowner';

                                $data['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $data['landowner_deed_number']=$landowner_deed_number.' Date:'.$recent_date;

                                $newJobParty = JobParty::create($data);

                           }

                        $matched=true;   

                        break;

                    }

                }

                if (!$matched){

                    $data['firm_name']=strtoupper($owner->name);

                    $data['latest_type']='owner';

                    $data['client_id']=$job->client_id;

                    $data['is_hot']=0;

                    $data['hot_id']=0;



                    $entity = Entity::create($data);



                    $xdata['first_name'] = strtoupper($owner->first_name);

                    $xdata['last_name'] = strtoupper($owner->last_name);

                    if (!$owner->first_name) $xdata['first_name'] ='';
                    if (!$owner->last_name) $xdata['last_name'] ='';
                    if (isset($owner->gender)) $xdata['gender'] = $owner->gender; else $xdata['gender']='none';

                    $xdata['address_1'] = strtoupper($owner->address);

                    $xdata['address_2'] = strtoupper($owner->address2);

                    $xdata['city'] = strtoupper($owner->city);

                    $xdata['state'] = strtoupper($owner->state);

                    $xdata['zip'] = $owner->zip_code;

                    //$xdata['phone'] = $owner->phone;
                    $xdata['country'] = 'USA';



                    $new_contact = ContactInfo::create($xdata);

                    $new_contact->entity_id = $entity->id;

                    $new_contact->primary = 1;

                    $new_contact->save();



                                $xdata['entity_id'] = $entity->id;

                                $xdata['contact_id'] = $new_contact->id;

                                $xdata['type'] = 'landowner';

                                $xdata['job_id'] = $job->id;

                                $landowner_deed_number='';
                                    if ($deed_book) $landowner_deed_number="Book: $deed_book";
                                    if ($landowner_deed_number) $landowner_deed_number=$landowner_deed_number." and ";    
                                    if ($deed_page) $landowner_deed_number=$landowner_deed_number." Page: $deed_page";

                                $xdata['landowner_deed_number']=$landowner_deed_number;

                                $newJobParty = JobParty::create($xdata);

                }

            }

            $job->search_status='done';
            $job->save();
        Session::flash('message', 'Re-run Search succeeded.');
        return redirect()->route('jobs.edit',$job->id);

         
    }
    public function recalculate_date($job){
      $works=$job->workorders->whereNotIn('work_orders.status',['completed','cancelled','cancelled charge','cancelled no charge','closed', 'temporary'])->where('work_orders.deleted_at',null);
        
      $job_started_at = date_create($job->started_at);
      $today=date_create(date('c'));
      $last_day = $job->last_day;
      $dif=date_diff($job_started_at,$today)->format('%a');
        

      foreach ($works as $work) {
        if ($work->type=='notice-to-owner' || $work->type=='amended-notice-to-owner') {
          if($dif>=36){
            $work->is_rush=1;
          }else{
            $work->is_rush=0;
          }
          $due_at=new \DateTime($job->started_at);
          $mailing_at=new \DateTime($job->started_at);
          $due_diff=new \DateInterval('P43D');
          $mailing_diff=new \DateInterval('P39D');
          $due_at->add($due_diff);
          $mailing_at->add($mailing_diff);
          
          $work->due_at=$due_at->format('Y-m-d H:i:s');
          $work->mailing_at=$mailing_at->format('Y-m-d H:i:s');


        }else{
          if ($work->type == 'claim-of-lien' || $work->type == 'notice-of-non-payment') {
            if (strlen($last_day) > 0) {
              $job_lastday = date_create($last_day);
              $dif=date_diff($job_lastday,$today)->format('%a');
              if($dif>=86){
                $work->is_rush=1;
              }else{
                $work->is_rush=0;
              }
              $due_at=new \DateTime($last_day);
              $mailing_at=new \DateTime($last_day);
              $due_diff=new \DateInterval('P89D');
              $mailing_diff=new \DateInterval('P89D');
              $due_at->add($due_diff);
              $mailing_at->add($mailing_diff);
              
              $work->due_at=$due_at->format('Y-m-d H:i:s');
              $work->mailing_at=$mailing_at->format('Y-m-d H:i:s');

            }

          }else{
            if($dif>=4){
              $work->is_rush=1;
            }else{
              $work->is_rush=0;
            }
            $due_at=new \DateTime($job->started_at);
            $mailing_at=new \DateTime($job->started_at);
            $due_diff=new \DateInterval('P10D');
            $mailing_diff=new \DateInterval('P7D');
            $due_at->add($due_diff);
            $mailing_at->add($mailing_diff);
            
            $work->due_at=$due_at->format('Y-m-d H:i:s');
            $work->mailing_at=$mailing_at->format('Y-m-d H:i:s');

            }

        }

        $work->save();
      }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $temp_name = $job->name;
        $len=count($job->workorders->where('status', '!=', 'temporary')->where('deleted_at', null));

        if ($len>0){
            Session::flash('message', 'This job has one or more work orders.  Please delete all work orders before deleting this job.');
        }else{
            $works=$job->workorders->where('status', 'temporary')->where('deleted_at', null);
            foreach ($works as $work) {
                $work->delete();
            }
            $job->delete();
            Session::flash('message', 'Job ' .$temp_name . ' successfully deleted.');
        }
        //return redirect()->to(($request->input('redirect_to')));
        return redirect()->route('jobs.index');
    }
    
    public function closeJob(Request $request,$id)
    {
        $job = Job::findOrFail($id);
        $temp_name = $job->name;
        $job->status = 'closed';
        $job->save();

        // redirect
        Session::flash('message', 'Job ' .$temp_name . ' successfully closed.');
        
        return redirect()->to(($request->input('redirect_to')));
    }

    public function customerlist(Request $request)
    {
        $search_query = $request->input('term');
        $entityIds = array_unique(JobParty::where('type','customer')->pluck('entity_id')->toArray());
        $customers = [];
        foreach (Entity::where('firm_name', 'like', "$search_query%")->get() as $customer) {
            if (!in_array($customer->id, $entityIds)) continue;
            $customers[] = $customer;
        }
        return json_encode($customers);
    }

    public function joblist($client_id, Request $request)
    {
        $search_query = $request->input('term');
        if ($client_id != 0 && $client_id != '0' && $client_id) {
            $jobs=  Job::where('name', 'like', "%$search_query%")->where('deleted_at', null)->where('client_id', $client_id)->get();
        } else {
            $jobs=  Job::where('name', 'like', "%$search_query%")->where('deleted_at', null)->get();
        }
        return json_encode($jobs->toArray());
    }
    public function listcontacts($id,Request $request)
    {
         $search_query = $request->input('term');
 //        $search_query='JESUS';
        
         $job = Job::findOrFail($id);
         $remove_contacts =$job->client->contacts->where('hot_id','<>',0)->pluck('hot_id');
         $client_contacts = $job->client->contacts->pluck('id')->where('status',1)->toArray();

        
         $entities= \App\Entity::search($search_query)->where('client_id',$job->client->id)->get()->pluck('id')->toArray();

         //$entities_local= \App\ContactInfo::search($search_query)->get()->where('status',1)->pluck('entity_id')->toArray();
 
         $entities_hot =  \App\Entity::search($search_query)->where('client_id',0)->get()->pluck('id')->toArray();

         $entities_hot_all =  \App\Entity::where('client_id',0)->orwhere('client_id',$job->client->id)->get()->pluck('id')->toArray();
                
         $all_entities = array_merge($entities,$entities_hot);
 
         $entity_contacts=  \App\ContactInfo::whereIn('entity_id',$all_entities)->where('status',1)->whereNotIn('id',$remove_contacts)->get();

         $contacts=  \App\ContactInfo::search($search_query)->get()->where('status',1)->whereIn('id',$client_contacts)->whereNotIn('id',$remove_contacts);
         $contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status',1)->where('is_hot',1)->whereNotIn('id',$remove_contacts);
         $contacts_hot_all = \App\ContactInfo::search($search_query)->get()->where('status',1)->whereIn('entity_id',$entities_hot_all)->whereNotIn('id',$remove_contacts);


         $all_contacts = $contacts->merge($contacts_hot);
         $all_contacts = $all_contacts->merge($contacts_hot_all);
         $all_contacts = $all_contacts->merge($entity_contacts)->sortBy('name_entity_name')->toArray();
         
         $all_contacts = array_values($all_contacts);

         return json_encode($all_contacts);
   
    }
    
    public function listjobs($id,Request $request)
    {
        $search_query = $request->input('term');
        
        // $clients = Client::search($search_query)->get()->pluck('id')->toArray();
        // $client_jobs = Job::whereIn('client_id',$clients)->get();
        // $jobs = Job::search($search_query )->get();
        // $merge = $jobs->merge($client_jobs);
        // $merge = $merge->whereNotIn('id',[$id]);
        // $all_contacts =$merge->toJson();

        $clients = Client::where('company_name','like','%'.$search_query.'%')->orwhere('first_name','like','%'.$search_query.'%')->orwhere('last_name','like','%'.$search_query.'%')->get()->pluck('id')->toArray();
        $client_jobs = Job::whereIn('client_id',$clients)->get();
        $jobs = Job::where('name','like','%'.$search_query.'%')->orwhere('address_1','like','%'.$search_query.'%')->orwhere('address_2','like','%'.$search_query.'%')->get();
        $merge = $jobs->merge($client_jobs);
        $merge = $merge->whereNotIn('id',[$id]);
        $all_contacts =$merge->toJson();

        return $all_contacts;
    }
   
    public function jobform($id) {
        $job = Job::findOrFail($id);
        
        $parties_type = [
            'client' => 'Client',
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'bond' => 'Bond Info',
            'landowner' => 'Property Owner',
            'leaseholder' => 'Lease Holder',
            'lender' => 'Lender',
            'copy_recipient'=> "Copy Recipient",
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
            
        ];
        
        $data =  [
            'parties_type' => $parties_type,
            'xjob' => $job,
        ];
        
        return view('admin.jobs.components.copydata',$data);
    }
    public function docopy(Request $request, $id) {
        //dd($request->all());
        $job = Job::findOrfail($id);
        $changes = false;
        if ($request->has('copy_legal')) {
            //dd("entre aqui");
            $job->legal_description = $request->xlegal_description;
            $changes = true;
        }
        $job->save();

        if ($request->has('copy_noc')) {
            $job->noc_number = $request->xnoc_number;
            $changes = true;
        }
        $job->save();
        if ($request->has('copy_folio')) {
            $job->folio_number = $request->xfolio_number;
            $changes = true;
        }
        $job->save();

        // Job NOCs
        if($request->has('copy_noc')) {
            foreach ($request->copy_noc as $noc_id => $value) {
                $noc = JobNoc::FindOrFail($noc_id);
                $new_noc = $noc->replicate();
                $new_noc->job_id = $id;
                $new_noc->save();
                if ($new_noc->copy_noc) {
                    $new_path = 'attachments/jobs/noc/'. $new_noc->id . '.' . substr($new_noc->copy_noc,-4);
                    $new_path = str_replace('..', '.', $new_path);
                    Storage::copy($new_noc->copy_noc, $new_path);
                    $new_noc->copy_noc = $new_path;
                    $new_noc->save();
                }
            }
        }
        
        if($request->has('copy_party')) {
            
            foreach ($request->copy_party as $party_id => $value) {
                $party = JobParty::findOrFail($party_id);
               
                $entity  = $party->firm;
               
                $contact = $party->contact;
                if ($entity->client_id == $job->client_id) {
                    
                    $new_party = $party->replicate();
                    $new_party->job_id = $id;
                    $new_party->save();
                     $changes = true;
                } else {
                    $entity_count = \App\Entity::where('firm_name',$entity->firm_name)->where('client_id',$job->client_id)->count();
                    if($entity_count > 0) {
                        $existent_entity = \App\Entity::where('firm_name',$entity->firm_name)->where('client_id',$job->client_id)->first();
                        $contact_count = \App\ContactInfo::where('entity_id',$existent_entity->id)
                                ->where('first_name',$contact->first_name)
                                ->where('last_name',$contact->last_name)
                                ->where('address_1',$contact->address_1)
                                ->where('address_2',$contact->address_2)->count();
                        if ( $contact_count > 0) {
                            // use existent contact and existent entity to recreate job party
                            $existent_contact = \App\ContactInfo::where('entity_id',$existent_entity->id)
                                ->where('first_name',$contact->first_name)
                                ->where('last_name',$contact->last_name)
                                ->where('address_1',$contact->address_1)
                                ->where('address_2',$contact->address_2)->first();
                            $new_party = $party->replicate();
                            $new_party->job_id = $id;
                            $new_party->entity_id = $existent_entity->id;
                            $new_party->contact_id = $existent_contact->id;
                            $new_party->save();
                             $changes = true;
                        } else {
                            // new Contact
                            $new_contact = $contact->replicate();
                            $new_contact->entity_id = $existent_entity->id;
                            $new_contact->save();
                            
                            $new_party = $party->replicate();
                            $new_party->job_id = $id;
                            $new_party->entity_id = $existent_entity->id;
                            $new_party->contact_id = $new_contact->id;
                            $new_party->save();
                             $changes = true;
                        }
                        // TODO exist let's search for contact
                    } else {
                        //does not exist lets replicate entity, contact, Party.
                        $new_entity = $entity->replicate();
                        $new_entity->client_id = $job->client_id;
                        $new_entity->save();
                        
                       
                        $new_contact = $contact->replicate();
                        $new_contact->entity_id = $new_entity->id;
                        $new_contact->save();
                        
                        $new_party = $party->replicate();
                        $new_party->job_id = $id;
                        $new_party->entity_id = $new_entity->id;
                        $new_party->contact_id = $new_contact->id;
                        $new_party->save();
                         $changes = true;
                        //TODO if is bond copy file in storage
                    }
                }
                
                if (strlen($new_party->bond_pdf) > 0) {
                    
                    $new_path = 'jobparties/bonds/pdfs/job-' . $new_party->job_id . '-party-' . $new_party->id . '.pdf';
                    Storage::copy($new_party->bond_pdf, $new_path);
                    $new_party->bond_pdf = $new_path;
                    $new_party->save();
                }
            }
        }
        
        // attachments
        if($request->has('copy_attachment')) {
                foreach ($request->copy_attachment as $attach_id => $value) {
                    $attach = Attachment::FindOrFail($attach_id);
                    $new_attachment = $attach->replicate();
                 
                    $new_attachment->attachable_id = $id;
                    $new_attachment->user_id = Auth::user()->id;
                    $new_attachment->attachable_type='App\Job';
                    $new_attachment->save();
                    
                    $new_path = 'attachments/jobs/' . $id. '/attachment-' . $new_attachment->id . substr($new_attachment->file_path,-4);
                    $new_path_thumb = 'attachments/jobs/' . $id. '/thumbnail-' . $new_attachment->id . substr($new_attachment->thumb_path,-4);
                    
                    Storage::copy($new_attachment->file_path, $new_path);
                    if ($new_attachment->thumb_path) { 
                      if(Storage::disk()->exists($new_attachment->thumb_path)){ 
                        Storage::copy($new_attachment->thumb_path, $new_path_thumb);
                        $new_attachment->thumb_path = $new_path_thumb;
                      }
                    }
                    
                    $new_attachment->file_path = $new_path;
                    
                    $new_attachment->save();
                }
             
        }
        
        if($changes) {
            Session::flash('message', 'Info selected succesfully copied to current job');
        }
        
        return redirect()->route('jobs.edit',$id);
    }


    public function uploadattachment($id,Request $request) {
        if ($request['file']==null || $request['file']=="" ) {
            Session::flash('message', 'file is required.');
            return redirect()->route('jobs.edit',['id'=>$id,'#attachments']);

        }

        $job = Job::findOrFail($id);
        $client =$job->client;
        // $this->validate($request, [
        //     'file' => 'required|file',   
        // ]);
        
        $attachment = new Attachment();
        $f = $request->file('file');

        $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
         
        if ($f->getSize()>$max_uploadfileSize){
            Session::flash('message', 'This file is too large to upload.');
            return redirect()->route('jobs.edit',['id'=>$id,'#attachments']);
        }        
        
        $attachment->type = $request->input('type');
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $job->attachments()->save($attachment);
        $attachment->save();
         
        $xfilename = "attachment-" .$attachment->id . "." . $f->guessExtension();
        $xpath = 'attachments/jobs/' . $id . '/';
        $f->storeAs($xpath,$xfilename);
        $attachment->file_path = $xpath .  $xfilename;
        $attachment->save();
        
        
        //dd($f->getMimeType());
        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new \Imagick();
                $img->readImageBlob($xblob);
                $img->setIteratorIndex(0);
                $img->setImageFormat('png');
                $img->setbackgroundcolor('rgb(64, 64, 64)');
                $img->thumbnailImage(300, 300, true, true);
                Storage::put($xpath . "thumbnail-" .$attachment->id . ".png",$img);
                $attachment->thumb_path = $xpath . "thumbnail-" .$attachment->id . ".png";
               
                break;
            case 'image/jpeg':
            case 'image/png':
                $xblob = file_get_contents($f->getRealPath());
                $img = new \Imagick();
                $img->readImageBlob($xblob);
                $img->setImageFormat('png');
                $img->setbackgroundcolor('rgb(64, 64, 64)');
                $img->thumbnailImage(300, 300, true, true);
                Storage::put($xpath . "thumbnail-" .$attachment->id . ".png",$img);
                $attachment->thumb_path = $xpath . "thumbnail-" .$attachment->id . ".png";
                break;
            default:
                $attachment->thumb_path = null;
                break;
        }

        if ($request->has('clientviewable')) {
            $attachment->clientviewable='no';
        }else{
            $attachment->clientviewable='yes';
        }
        $attachment->save();
        
        if ($request->custom_message) {
            $note = New Note();
            $now = Carbon::now();
            $note->note_text = $request->custom_message;
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = 1;
            $note->viewable = 1;
            $note->noteable_type = 'App\Job';
            $note->client_id=$client->id;
            $note = $job->notes()->save($note);
        }
        
        if ($request->has('notify')) {
            $data = [
                'note' => 'Have been added to a Job',
                'entered_at' => $attachment->created_at->format('Y-m-d H:i:s')
            ];
            //if ($client->notification_setting=='immediate'){
            $work = $job->workorders()->where('status', '!=', 'temporary')->orderBy('id', 'desc')->first();
            $notifiable_user = $client->activeusers;
            if ($work) {
                if ($work->responsible_user) {
                    $responsible_user = User::where('id', $work->responsible_user)->get();
                    if (count($responsible_user)>0) $notifiable_user = $responsible_user;
                }
            }    
            Notification::send($notifiable_user, new NewAttachment($attachment->id,$data,$request->custom_message,Auth::user()->full_name,'job'));

            if ($job->notify_email) {
                $notify_user = TempUser::create(['email'=>$job->notify_email]);
                Notification::send($notify_user, new NewAttachment($attachment->id,$data,$request->custom_message,Auth::user()->full_name,'job'));
                $notify_user->delete();
            }
            //}
        }
        Session::flash('message', 'Attachment added');
        return redirect()->route('jobs.edit',['id'=>$id,'#attachments']);
    }
    
    
    public function showattachment($job_id,$id) {
        $is_attach=Attachment::where('id',$id)->get();
        if (count($is_attach)<1){
            Session::flash('message', 'This attachment was deleted.');
            return redirect()->route('workorders.edit',['id'=>$job_id,'#attachments']);
        }
        
        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200',[
            'Content-Type' => $attachment->file_mime,
            'Content-Disposition' => 'attachment; filename="' . $attachment->original_name . '"',
            ]);
       
        return $response;
        
    }
    
    public function showthumbnail($job_id,$id) {
        $attachment = Attachment::findOrFail($id);
        if (is_null($attachment->thumb_path)) {
            switch($attachment->file_mime) {
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 
                    $contents = file_get_contents(public_path('images/word.png'));
                    break;
                default:
                    $contents = file_get_contents(public_path('images/file.png'));
                    break;
            }
        } else {
            $contents = Storage::get($attachment->thumb_path);
        }
        
        $response = Response::make($contents, '200');
        $response->header('Content-Type', 'image/png');
        return $response;
        
    }
    
    
    public function destroy_attachment($id) {
        $attachment = Attachment::findOrFail($id);
        $job_id = $attachment->attachable_id;
        if (is_null($attachment->thumb_path)) {
            
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();
        
        Session::flash('message', 'Attachment removed');
        return redirect()->route('jobs.edit',['id'=>$job_id,'#attachments']);
    }
    
    public function setfilter (Request $request) {
        
        if ($request->has('resetfilter')) {
               if($request->resetfilter=='true') {
                   session()->forget('work_order_filter');
               }
        }
        
       if ($request->has('job_name')) {
           
            if($request->job_name == '' ) {
                session()->forget('job_filter.name');
            } else {
                session(['job_filter.name' => $request->job_name]);
            }
        }

        if ($request->has('client_filter')) {
            if($request->client_filter == 0 ) {
                session()->forget('job_filter.client');
            } else {
                session(['job_filter.client' => $request->client_filter]);
            }
        }
        
        if ($request->has('job_type')) {
           
            if($request->job_type == 'all' ) {
                session()->forget('job_filter.job_type');
            } else {
                session(['job_filter.job_type' => $request->job_type]);
            }
        }

        if ($request->has('job_status')) {
            if($request->job_status == 'none' ) {
                session()->forget('job_filter.job_status');
            } else {
                session(['job_filter.job_status' => $request->job_status]);
            }
        }
         if ($request->has('daterange')) {
            if($request->daterange == '' ) {
                session()->forget('job_filter.daterange');
            } else {
                session(['job_filter.daterange' => $request->daterange]);
            }
         }
        return redirect()->route('jobs.index');
    }
    
    
    public function resetfilter (Request $request) {
         session()->forget('job_filter');
        return redirect()->route('jobs.index');
    }

    public function setfilterLog (Request $request, $id) {
        session()->forget('joblog_filter.fullname');
        session()->forget('joblog_filter.daterange');
        if ($request->has('fullname')) {
            if($request->fullname) {
                session(['joblog_filter.fullname' => $request->fullname]);
            }
        }

        if ($request->has('daterange')) {
            if($request->daterange) {
                session(['joblog_filter.daterange' => $request->daterange]);
            }
        }
        return redirect()->route('jobs.logs', $id);
    }

    public function deleteLog (Request $request, $id) {
        $log=JobLog::where('id', $request->log_id)->first();
        $log->delete();
        return redirect()->route('jobs.logs', $id);
    }
    
    
    public function resetfilterLog (Request $request, $id) {
        session()->forget('joblog_filter');
        return redirect()->route('jobs.logs', $id);
    }
    
    public function getNumber ($id) {
        $job = Job::findOrFail($id);
        return $job->number;
    }
    
    public function getContractAmount ($id) {
        $job = Job::findOrFail($id);
        return '$ ' . number_format($job->contract_amount,2);
    }
    
     public function getStartedAt ($id) {
        $job = Job::findOrFail($id);
        return $job->started_at;
    }
    
    // public function getLastDay ($id) {
    //     $job = Job::findOrFail($id);
    //     return $job->last_day->format('m/d/Y');
    // }
    public function getLastDay ($id) {
        $job = Job::findOrFail($id);
        return ($job->last_day) ? $job->last_day->format('m/d/Y') : '';
    }

    public function getUsers ($id) {
        $job = Job::findOrFail($id);
        $users = $job->client->activeusers;
        return response()->json($users);
    }

    public function getaddress(Request $request){
      $county=$request->county;
      $address_1=$request->address_1;
      return PropertyRecords::where('property_county',$county)->where('property_address1_first_word',$address_1)->get();
    }
    public function search_address(Request $request){
      $county=$request->county;
      $full_address=$request->full_address;
      $property=PropertyRecords::where('property_county',$county)->where('property_address_full',$full_address)->get();
      if (count($property)>0){
        $data['property']=$property;
        $data['result']='matched'; 
        return $data; 
      }
      $address_words = explode(" ", $full_address);
      $start_word=$address_words[0];
      $property=PropertyRecords::where('property_county',$county)->where('property_address1_first_word',$start_word)->get();
      if (count($property)>0){
        $data['property']=$property;
        $data['result']='like'; 
        return $data; 
      }
      $data['property']=array();
      $data['result']='none'; 
      return $data; 

    }
    public function search_jobs(Request $request){
        $search = $request->search;
        $jobs_list = Job::where('name', 'like', "%$search%")->where(function($q) {
            $q->where('status','!=','closed')->orwhereNull('status');
        })->get();
        return response()->json($jobs_list);
    }

    public function submiteToResearch($id)
    {
        $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->back();
        }
        $job->research_complete = null;
        $job->research_start = null;
        $job->save();

        $work = $job->firstWorkorder();
        if($work) {
            $work->researcher = null;
            $work->save();
        }

        Session::flash('message', 'Job was submitted to Research queue.');
        return redirect()->back();
    }

    public function markCompleted($id) {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('jobs.index');
        }
        $job = Job::findOrFail($id);
        $now = Carbon::now();
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('jobs.index');
    }

    public function createJobLogResearchComplete($job, $changeArray) {
        if (count($changeArray)>0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => json_encode($changeArray),
            ]);
        }
    }

    public function share(Request $request, $job_id) {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user) {
            Session::flash('message', 'Error: You tried to share the job to invalid user email.');
            return redirect()->back();
        }
        $job = Job::where('id', $job_id)->where('deleted_at', null)->first();
        if (!$job) {
            Session::flash('message', 'Error: The job was deleted.');
            return redirect()->back();
        }
        if ($user->client && !$user->client->is_monitoring_user) {
            Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), false));
            $admin = new User(); $admin->email = 'Suzanne@sunshinenotices.com';
            Notification::send($admin, new ShareJobToMornitoringUser($job, Auth::user(), false));
            Session::flash('message', 'Error: The user is not a mornitoring user. Will you set up the user to mornitoring user?');
            return redirect()->back();
        }

        $shared = SharedJobToUser::where('user_id', $user->id)->where('job_id', $job_id)->first();
        if ($shared) {
            Session::flash('message', 'Error: The job was already shared to '. $user->full_name);
            return redirect()->back();
        }

        $shared = SharedJobToUser::create(
            [
                'user_id' => $user->id,
                'job_id' => $job_id
            ]
        );
        Notification::send($user, new ShareJobToMornitoringUser($job, Auth::user(), true));
        Session::flash('message', 'The job was shared to '. $user->full_name);
        return redirect()->back();
    }
}
