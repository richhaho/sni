<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SharedJobToUser;
use App\User;
use App\Job;
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
class JobsMonitorController extends Controller
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


       
       if (session()->has('job_monitor_filter.name')) {
          $jobs->where('name','LIKE','%' . session('job_monitor_filter.name') .'%');
       }
       
       if (session()->has('job_monitor_filter.client')) {
          $jobs->where('client_id',session('job_monitor_filter.client'));
       }
       if (session()->has('job_monitor_filter.job_type')) {
           if(session('job_monitor_filter.job_type') != 'all') {
               $jobs->where('type',session('job_monitor_filter.job_type'));
           }
       }
       if (session()->has('job_monitor_filter.job_status')) {
           if(session('job_monitor_filter.job_status') != 'none') {
               if(session('job_monitor_filter.job_status') != 'null'){  
                 $jobs->where('status',session('job_monitor_filter.job_status'));
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
       
        if (session()->has('job_monitor_filter.daterange')) {
            if(session('job_monitor_filter.daterange') != '') {
                $date_range=session('job_monitor_filter.daterange');
                $date_type="/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/";
                
               if (preg_match($date_type,$date_range)){

                $dates = explode(' - ',session('job_monitor_filter.daterange'));
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
         
       return view('admin.jobs_monitor.index',$data);
    }

    public function setfilter (Request $request) {
        
        if ($request->has('resetfilter')) {
               if($request->resetfilter=='true') {
                   session()->forget('work_order_filter');
               }
        }
        
       if ($request->has('job_name')) {
           
            if($request->job_name == '' ) {
                session()->forget('job_monitor_filter.name');
            } else {
                session(['job_monitor_filter.name' => $request->job_name]);
            }
        }

        if ($request->has('client_filter')) {
            if($request->client_filter == 0 ) {
                session()->forget('job_monitor_filter.client');
            } else {
                session(['job_monitor_filter.client' => $request->client_filter]);
            }
        }
        
        if ($request->has('job_type')) {
           
            if($request->job_type == 'all' ) {
                session()->forget('job_monitor_filter.job_type');
            } else {
                session(['job_monitor_filter.job_type' => $request->job_type]);
            }
        }

        if ($request->has('job_status')) {
            if($request->job_status == 'none' ) {
                session()->forget('job_monitor_filter.job_status');
            } else {
                session(['job_monitor_filter.job_status' => $request->job_status]);
            }
        }
         if ($request->has('daterange')) {
            if($request->daterange == '' ) {
                session()->forget('job_monitor_filter.daterange');
            } else {
                session(['job_monitor_filter.daterange' => $request->daterange]);
            }
         }
        return redirect()->route('jobs_monitor.index');
    }
    
    
    public function resetfilter (Request $request) {
        session()->forget('job_monitor_filter');
        return redirect()->route('jobs_monitor.index');
    }
}
