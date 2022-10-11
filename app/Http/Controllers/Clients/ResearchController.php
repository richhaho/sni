<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\TempUser;
use App\Job;
use App\JobLog;
use App\JobParty;
use App\ContactInfo;
use App\Entity;
use App\WorkOrderType;
use App\WorkOrder;
use App\Client;
use App\Coordinate;
use App\Site;
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
use DateTime;
//composer require guzzlehttp/guzzle:~6.0
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as Http_Client;
use App\PropertyRecords;
class ResearchController extends Controller
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
        return redirect()->route('client.jobs.index');
        session()->forget('research_job_id');
        $user_id = Auth::user()->id;
        $client = Auth::user()->client;
        $jobs = Job::query()->where('status', '!=', 'closed')->where('client_id', $client->id)->where('research_complete', null)->whereHas('workorders',function($p) use ($user_id) {
            $p->where('type', 'notice-to-owner')->where(function($q) {
                $q->where('service', null)->orwhere('service', 'full');
            })->where('deleted_at', null)->where('status','open')->where(function($q) use ($user_id) {
                $q->where('researcher', null)->orwhere('researcher', $user_id);
            });
        });

        if (session()->has('research_filter.county')) {
          $jobs->where('county','LIKE','%' . session('research_filter.county') .'%');
        }
        if (session()->has('research_filter.has_corners')) {
            if (session('research_filter.has_corners') == '0') {
                $jobs->where('address_corner', null);
            }
            if (session('research_filter.has_corners') == '1') {
                $jobs->where('address_corner', '!=', null);
            }
        }

        $joblist = $jobs->get();
        $jobIdsRused = [];
        foreach($joblist as $jb) {
            if ($jb->firstWorkorder() && $jb->firstWorkorder()->is_rush) {
                $jobIdsRused[] = $jb->id;
            }
        }

        if (session()->has('research_filter.work_rush')) {
            if (session('research_filter.work_rush') == '0') {
                $jobs->whereNotIn('id', $jobIdsRused);
            }
            if (session('research_filter.work_rush') == '1') {
                $jobs->whereIn('id', $jobIdsRused);
            }
        }
       
        $jobs = $jobs->orderBy('id','ASC')->paginate(15);
        Session::put('backUrl',\URL::full());
        $data = [
            'jobs' => $jobs,
        ];
         
       return view('client.research.index',$data);
    }

    // Research Start
    public function start(Request $request, $id){
        $job=Job::where('id',$id)->first();
        $work = $job->firstWorkorder();

        if (!$work) {
            Session::flash('message', "Error: The job does not have NTO.");
            return redirect()->back();
        }

        if ($job->research_start && $work->researcher != Auth::user()->id) {
            $researcher = $work->researcherUser() ? $work->researcherUser()->fullName : '';
            Session::flash('message', "Researcher {$researcher} is already assigned to this work order.");
            return redirect()->back();
        }
        $authUserId = Auth::user()->id;
        // $startedJob = Job::where('id', '!=', $id)->where('research_start', '!=', null)->where('research_complete', null)->whereHas('workorders',function($p) use ($authUserId) {
        //     $p->where('researcher', $authUserId);
        // })->first();
        // if ($startedJob) {
        //     $workNumber =  $startedJob->firstWorkorder()->number;
        //     Session::flash('message', "You are already working on a different job. Please complete work order {$workNumber} before starting this one.");
        //     return redirect()->back();
        // }
        $work->researcher = $authUserId;
        $work->save();
        $now = Carbon::now();
        $changeArray = $job->getChanges(['research_start' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_start = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        session(['research_job_id' => $id]);
        return redirect()->route('client.research.edit', $id);
    }

   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.edit', session('research_job_id'));
        }
        
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
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
        $work_orders=WorkOrder::where('job_id',$id)->get();

        
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
        $markStatuses = [
            'edit' => 'Edit',
            'tax rolls' => 'Tax rolls',
            'phone calls' => 'Phone calls'
        ];
        $entities = $job->client->entities->pluck('firm_name','id');
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male'
        ];
        $parties_type = [
            ''=>'',
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
        $parties_type1 = [
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
          'coordinates' => $coordinates,
          'markStatuses' => $markStatuses,
          'entities' => $entities,
          'gender' => $gender,
          'parties_type' => $parties_type,
          'parties_type1' => $parties_type1,
          'step' => 'edit'
         ];
        return view('client.research.edit',$data);
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
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        if (!$request['client_id']){
            $data['client_id'] = $job->client_id;
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
            return redirect()->route('client.research.edit',$job->id);
        } else {
            return redirect()->route('workorders.edit',$request->input('workorder'));
        }
    }

    public function markCompleted($id, Request $request) {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = $request->status;
        $work->save();
        $now = Carbon::now();
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }

    public function wizardStep1($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step1', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);

        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }


        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [ 
            'job' =>$job,
            'match_jobs' =>$match_jobs
        ];
        return view('client.research.wizard.step1',$data);
    }

    public function wizardStep1Update(Request $request, $id)
    {
        $this->validate($request, [
          'city' => 'required',
        ]);
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $data = $request->all();
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
        if ($request->isSave == 'yes') {
            return redirect()->back();
        }
        return redirect()->route('client.research.wizard.step2',$job->id);
    }

    public function wizardStep2($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step2', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [ 
          'job' =>$job,
          'match_jobs' =>$match_jobs
        ];
        return view('client.research.wizard.step2',$data);
    }
  
    public function wizardStep2Update(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'tax rolls';
        $work->save();

        $note = New Note();
        $now = Carbon::now();
        $note->note_text = 'Job address provided by client does not match the tax roll address. '; //. 'Research started: '. date('Y-m-d H:i:s',  strtotime($job->research_start)) . '   Research completed: ' . date('Y-m-d H:i:s',  strtotime($now));
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        $note->viewable = 0;
        $note->noteable_type = 'App\Job';
        $note->client_id=$job->client->id;
        $note = $job->notes()->save($note);
        
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }


    public function wizardStep3($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step3', session('research_job_id'));
        }
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $entities = $job->client->entities->pluck('firm_name','id');
        $xworkorder = $job->firstWorkorder();

        $parties_type = [
            'landowner' => 'Property Owner',
        ];
        $parties_type1 = [
            'landowner' => 'Property Owner'
        ];
            
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male'
        ];

        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        
        $data = [
            'job'=> $job,
            'match_jobs'=> $match_jobs,
            'parties_type' =>$parties_type,
            'parties_type1' =>$parties_type1,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
            'step' => '3'
        ];
            
        return view('client.research.wizard.step3',$data);
    }

    public function wizardStep3Update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        return redirect()->route('client.research.wizard.step4',$id);
    }

    public function wizardStep4($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step4', session('research_job_id'));
        }
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
            if (!$job->research_start) {
                Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
                return redirect()->route('client.jobs.edit', $id);
            }
        $entities = $job->client->entities->pluck('firm_name','id');
        $xworkorder = $job->firstWorkorder();

        $parties_type = [
            'general_contractor' => 'General Contractor',
            'landowner' => 'Property Owner'
        ];
        $parties_type1 = [
            'general_contractor' => 'General Contractor',
            'landowner' => 'Property Owner'
        ];
            
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male'
        ];
        $attachment_types= AttachmentType::where('slug','!=','generated')->get()->pluck('name','slug');
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [
            'job'=> $job,
            'match_jobs'=> $match_jobs,
            'parties_type' =>$parties_type,
            'parties_type1' =>$parties_type1,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
            'attachment_types' => $attachment_types,
            'step' => '4'
        ];
            
        return view('client.research.wizard.step4',$data);
    }

    public function wizardStep4Update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        return redirect()->route('client.research.wizard.step5',$id);
    }
    

    public function wizardStep5($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step5', session('research_job_id'));
        }
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [ 
          'job' =>$job,
          'match_jobs' =>$match_jobs
        ];
        return view('client.research.wizard.step5',$data);
    }
  
    public function wizardStep5Update(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'qc';
        $work->save();

        $note = New Note();
        $now = Carbon::now();
        $note->note_text = 'NOC Owner does not match Tax Roll Owner - Please contact Order by or GC.'. 'Research started: '. date('Y-m-d H:i:s',  strtotime($job->research_start)) . '   Research completed: ' . date('Y-m-d H:i:s',  strtotime($now));
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        $note->viewable = 0;
        $note->noteable_type = 'App\Job';
        $note->client_id=$job->client->id;
        $note = $job->notes()->save($note);
        
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }

    public function wizardStep6($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step6', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [ 
          'job' =>$job,
          'match_jobs' =>$match_jobs
        ];
        return view('client.research.wizard.step6',$data);
    }
  
    public function wizardStep6Update(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'tax rolls';
        $work->save();

        $note = New Note();
        $now = Carbon::now();
        $note->note_text = 'Folio/Parcel ID on Taxrolls does not match the NOC.';//. 'Research started: '. date('Y-m-d H:i:s',  strtotime($job->research_start)) . '   Research completed: ' . date('Y-m-d H:i:s',  strtotime($now));;
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        $note->viewable = 0;
        $note->noteable_type = 'App\Job';
        $note->client_id=$job->client->id;
        $note = $job->notes()->save($note);
        
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }

    public function wizardStep7($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step7', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $entities = $job->client->entities->pluck('firm_name','id');
        $xworkorder = $job->firstWorkorder();
  
        $parties_type = [
          ''=>'',
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
        $parties_type1 = [
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
          
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male'
        ];
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $data = [
            'job'=> $job,
            'match_jobs'=> $match_jobs,
            'parties_type' =>$parties_type,
            'parties_type1' =>$parties_type1,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
            'step' => '7'
        ];
          
        return view('client.research.wizard.step7',$data);
      }
  
      public function wizardStep7Update(Request $request, $id)
      {
          return redirect()->route('client.research.wizard.step8',$id);
      }

    public function wizardStep8($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step8', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        $parties_type = [
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'sub_contractor' => "Sub Contractor",
        ];
        $data = [ 
          'job' =>$job,
          'match_jobs' =>$match_jobs,
          'work_order' => $job->firstWorkorder(),
          'parties_type' =>$parties_type,
          'parties_type1' =>$parties_type,
          'step'=>8
        ];
        return view('client.research.wizard.step8',$data);
    }
  
    public function wizardStep8Update(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'qc';
        $work->save();

        $note = New Note();
        $now = Carbon::now();
        $note->note_text = "Need to know who client's customer is working for on this job.". 'Research started: '. date('Y-m-d H:i:s',  strtotime($job->research_start)) . '   Research completed: ' . date('Y-m-d H:i:s',  strtotime($now));;
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        $note->viewable = 0;
        $note->noteable_type = 'App\Job';
        $note->client_id=$job->client->id;
        $note = $job->notes()->save($note);
        
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }

    public function wizardStep9($id) {
        if (!session('research_job_id')) {
            return redirect()->route('client.jobs.edit', $id);
        }
        if (session('research_job_id') != $id) {
            return redirect()->route('client.research.wizard.step9', session('research_job_id'));
        }

        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }

        if ($job->type=='private') {
            $now = Carbon::now();
            $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
            $job->research_complete = $now;
            $this->createJobLogResearchComplete($job, $changeArray);
            $job->save();
            $work = $job->firstWorkorder();
            $work->status = 'edit';
            $work->save();
            Session::flash('message', 'Research completed the job ' .$job->name . '.');
            return redirect()->route('client.jobs.edit', $id);
        }

        $entities = $job->client->entities->pluck('firm_name','id');
        $xworkorder = $job->firstWorkorder();

        $parties_type = [
            ''=>'',
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
        $parties_type1 = [
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
            
        $gender = [
            'none' => 'Select one..',
            'female' => 'Female',
            'male' => 'Male'
        ];

        $attachment_types= AttachmentType::where('slug','!=','generated')->get()->pluck('name','slug');
        $match_jobs = Site::where('county', $job->county)->get()->pluck('name', 'url')->prepend('', '')->toArray();
        
        $data = [
            'job'=> $job,
            'match_jobs'=> $match_jobs,
            'attachment_types'=> $attachment_types,
            'parties_type' =>$parties_type,
            'parties_type1' =>$parties_type1,
            'work_order' => $xworkorder,
            'entities' => $entities,
            'gender' => $gender,
            'step' => '9'
        ];
        return view('client.research.wizard.step9',$data);
    }
  
    public function wizardStep9Update(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'qc';
        $work->save();

        $note = New Note();
        $now = Carbon::now();
        $note->note_text = "Unable to locate bond for Public job.";
        $note->entered_at = $now->toDateTimeString();
        $note->entered_by = Auth::user()->id;
        $note->viewable = 0;
        $note->noteable_type = 'App\Job';
        $note->client_id=$job->client->id;
        $note = $job->notes()->save($note);
        
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }
    public function wizardStep9UpdateNumber(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        
        $changeArray = $job->getChanges(['number' => $request->number]);
        $changes = json_encode($changeArray);
        $job->project_number = $request->number;
        $job->save();
        if (count($changeArray)>0) {
            JobLog::create([
                'job_id' => $job->id,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->fullName,
                'edited_at' => Carbon::now(),
                'data' => $changes,
            ]);
        }
        return 'ok';
    }
    public function finishWizard(Request $request, $id)
    {
        $existsJob=Job::where('id',$id)->get();
        if (count($existsJob)<1){
            Session::flash('message', 'Job ' .$id . ' has been deleted already.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $job = Job::findOrFail($id);
        if (!$job->research_start) {
            Session::flash('message', 'Workorder ' .$job->firstWorkorder()->number . ' has been unassigned from you.');
            return redirect()->route('client.jobs.edit', $id);
        }
        $work = $job->firstWorkorder();
        $work->status = 'edit';
        $work->save();
        
        $now = Carbon::now();
        $changeArray = $job->getChanges(['research_complete' => date('Y-m-d H:i:s',  strtotime($now))]);
        $job->research_complete = $now;
        $this->createJobLogResearchComplete($job, $changeArray);
        $job->save();
        
        Session::flash('message', 'Research completed the job ' .$job->name . '.');
        return redirect()->route('client.jobs.edit', $id);
    }


































    public function editParty(Request $request,$job_id, $id) {
      $job = Job::findOrFail($job_id);
      $job_party =JobParty::findOrFail($id);
      $from = $request->from;
      $parties_type = array();
      if ($from=='step3') {
        $parties_type = [
            'landowner' => 'Property Owner'
        ];
      } elseif ($from=='step4') {
        $parties_type = [
          'general_contractor' => 'General Contractor',
          'landowner' => 'Property Owner'
        ];
      } elseif ($from=='step7' || $from=='step9' || $from=='stepedit') {
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
      } elseif ($from=='step8') {
        $parties_type = [
            'customer' => 'Customer',
            'general_contractor' => 'General Contractor',
            'sub_contractor' => "Sub Contractor",
        ];
      }
      $data = [
          'job_party'=> $job_party,
          'parties_type'=>$parties_type,
          'work_order' => $job->firstWorkorder(),
          'from'=> $from
      ];
      return view('client.research.wizard.editparty',$data);
    }

    public function updateParty(Request $request,$job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $this->validate($request, [
                'address_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required'
            ]); 
        $job_party = JobParty::findOrFail($id);
        $job_party->source = $request->source;
        $entity = $job_party->firm;
        if ($request->input('firm_name')!=null && $request->input('firm_name')!="")
        {
            $entity->firm_name = $request->input('firm_name');
            $entity->save();
        }
       
        
        $contact = $job_party->contact;
        
        if($request->input('first_name')!=null && $request->input('first_name')!=""){
            $contact->first_name =$request->input('first_name');}else{
                $contact->first_name ="";
            }
        if($request->input('last_name')!=null && $request->input('last_name')!=""){
            $contact->last_name = $request->input('last_name');}else{
                $contact->last_name ="";
            }
            
        $contact->address_1 = $request->input('address_1');
        $contact->address_2 = $request->input('address_2');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->mobile = $request->input('mobile');
        $contact->fax = $request->input('fax');
        $contact->city = $request->input('city');
        $contact->state = $request->input('state');
        $contact->zip = $request->input('zip');
        $contact->country = $request->input('country');
        //$contact->gender = $request->input('gender');
        if ($contact->hot_id == 0 ) {
             $contact->save();
             $entity->save();
        } else {
            if ($request->has('update_open_jobs')) {
                 $contact->save();
                 $entity->save();
            } else {
               if ($contact->isDirty()) {
                   $xcontact = new ContactInfo();
                   $xcontact = $contact->replicate();
                   $xcontact->hot_id = 0;
                   $xcontact->primary = 0;
                   $xcontact->save();
                   $job_party->contact_id =  $xcontact->id;
                   $job_party->save();
               } else {
                  
               }
            }
            
        }
        $data = $request->all();        
        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0 ) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = NULL;
            }
        }
        $job_party->update($data);
        if($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = "job-" .$job_id. "-party-" . $JobParty->id . "." . $f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath,$xfilename);
                $job_party->bond_pdf_filename = $f->getClientOriginalName();
                $job_party->bond_pdf = $xpath . "/" .$xfilename;
                $job_party->bond_pdf_filename_mime = $f->getMimeType();
                $job_party->bond_pdf_filename_size = $f->getSize();
                $job_party->save();
            } else {
               Session::flash('message', 'PDF file not uploaded correctly');
               return redirect()->back()->withInput(); 
            }
        }
        
        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0 ) {
                $job_party->bond_date = date('Y-m-d', strtotime($data['bond_date']));
            } else {
                 $job_party->bond_date = NULL;
            }
            $job_party->save();
        }
        
        if ($request->has('lien_prohibition')) {
            $job_party->landowner_lien_prohibition = 1;
        } else {
            $job_party->landowner_lien_prohibition = 0;
        }
        
        
        if ($request->has('copy_recipient_type')) {
            if ($request->copy_recipient_type == 'other' ) {
                $job_party->copy_type = $request->other_copy_recipient_type;
            } else {
                $job_party->copy_type = $request->copy_recipient_type;
            }
        }
        
        if ($request->has('leaseholder_type')) {
            if ($request->leaseholder_type == 'Lessee' ) {
                 $job_party->leaseholder_bookpage_number =NULL ;
            } else {
               
            }
        }
        $job_party->save();
        $from = $request->from;
        Session::flash('message', 'Job party ' .$job_party->contact->full_name . ' successfully created.');
        return $from=='stepedit' ?  redirect()->to(route('client.research.edit',$job_id).'?#parties') : redirect()->route('client.research.wizard.'.$from,$job_id);    
    }

    public function storeParty(Request $request, $job_id)
    {
        $job = Job::findOrFail($job_id);
        $data = $request->all();
        if ($request->exists('first_name')) {  
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'firm_name' => 'required_without_all:first_name,last_name,entity_id',
                'first_name' => 'required_without_all:firm_name,entity_id',    
                'last_name' => 'required_without_all:firm_name,entity_id',
                'address_1' => 'required_without:contact_id',
                'city' => 'required_without:contact_id',
                'state' => 'required_without:contact_id',
                'country' => 'required_without:contact_id'
            ]); 
            
            if($request->has('entity_id')) {
                //dd('por aqui');
                $entity = Entity::findOrFail($request->input('entity_id'));
                $xdata = $request->all();
                if (strlen($xdata['first_name'])==0) {
                    $xdata['first_name'] = " ";
                }
                if (strlen($xdata['last_name'])==0) {
                   $xdata['last_name'] = " ";
                }
                
                
                $contact = ContactInfo::create($xdata);
                $contact->entity_id = $request->input('entity_id');
                $contact->save();
            } else {
                $data['latest_type'] = $request->input('type');
                $entity = Entity::create($request->all());
                
                $xdata = $request->all();
                if (strlen($xdata['first_name'])==0) {
                    $xdata['first_name'] = " ";
                }
                if (strlen($xdata['last_name'])==0) {
                   $xdata['last_name'] = " ";
                }
                
                
                
                $contact = ContactInfo::create($xdata);

                $contact->entity_id = $entity->id;
                $contact->primary = 1;
                $contact->save();

                if ($request->input('firm_name') == "") {
                    $entity->firm_name = trim($contact->first_name . " " . $contact->last_name);
                    $entity->save();
                }
            }
            $data['contact_id'] = $contact->id;
            
        } else { 
            $this->validate($request, [
                'job_id' => 'required|exists:jobs,id',
                'type' => 'required',
                'contact_id' => 'required',
            ]);   
            
        
            $contact = ContactInfo::findOrFail($data['contact_id']);
            
            if($contact->is_hot){
                $xjob = Job::find($job_id);
                $existent_contact = $xjob->client->contacts()->where('contact_infos.hot_id',$contact->id)->first();
                if ($existent_contact) {
                   $contact = $existent_contact;
                } else {
                    $existent_entity = Entity::where([['hot_id', $contact->entity->id],['client_id',$xjob->client_id]])->first();
                    if ($existent_entity) {
                        $entity = $existent_entity;
                    } else {
                        $entity = new Entity();
                        $entity = $contact->entity->replicate();
                        $entity->client_id = $xjob->client->id;
                        $entity->hot_id = $contact->entity->id;
                        $entity->save();
                    }
                    $new_contact = New ContactInfo();
                    $new_contact = $contact->replicate();
                    $new_contact->hot_id = $contact->id;
                    $new_contact->entity_id = $entity->id;
                    $new_contact->save();
                    $new_contact->refresh();
                    
                    $contact = $new_contact;
                }
                $data['contact_id'] = $contact->id;
               
            }
        }
        $contact->refresh();
        $entity = $contact->entity;
        
        $data['entity_id'] = $entity->id;
        
        if ($request->type == 'copy_recipient') {
            if ($request->copy_recipient_type == 'other' ) {
                $data['copy_type'] = $request->other_copy_recipient_type;
            } else {
                $data['copy_type'] = $request->copy_recipient_type;
            }
        }
        
         if ($request->type == 'leaseholder') {
            if ($request->leaseholder_type == 'Lessee' ) {
                $data['leaseholder_bookpage_number'] = NULL;
            } else {
               
            }
        }
        
        if ($request->has('lien_prohibition')) {
            $data['landowner_lien_prohibition'] = 1;
        }

        

        if ($request->has('bond_date')) {
            if (strlen($data['bond_date']) > 0 ) {
                $data['bond_date'] = date('Y-m-d H:i:s', strtotime($data['bond_date']));
            } else {
                $data['bond_date'] = NULL;
            }
        }
        $JobParty = JobParty::create($data);
        $JobParty->source = $request->source;
        
        if($request->hasFile('bond_pdf')) {
            if ($request->file('bond_pdf')->isValid()) {
                $f = $request->file('bond_pdf');
                $xfilename = "job-" .$job_id. "-party-" . $JobParty->id . "." . $f->guessExtension();
                $xpath = 'jobparties/bonds/pdfs';
                $f->storeAs($xpath,$xfilename);
                $JobParty->bond_pdf_filename = $f->getClientOriginalName();
                $JobParty->bond_pdf = $xpath . "/" .$xfilename;
                $JobParty->bond_pdf_filename_mime = $f->getMimeType();
                $JobParty->bond_pdf_filename_size = $f->getSize();
                $JobParty->save();
            } else {
               Session::flash('message', 'PDF file not uploaded correctly');
               return redirect()->back()->withInput(); 
            }
        }
        $from = $request->from;
        Session::flash('message', 'Job party ' .$JobParty->contact->full_name . ' successfully created.');
        if ($from=='edit') {
            return redirect()->to(route('client.research.edit', $job_id).'?#parties');
        } else {
            return redirect()->route('client.research.wizard.'.$from, $job_id);
        }
    }









    public function copyParty(Request $request, $job_id, $id) {
      $job_party = JobParty::findOrFail($id);
      $copy = $job_party->replicate();
      $copy->type = $request->party_type;
      $copy->save();
      Session::flash('message', 'Job party ' . $job_party->contact->full_name . ' successfully copied.');
      return $request->from == 'stepedit' ? redirect()->to(route('client.research.edit', $job_id).'?#parties') : redirect()->back();    
    }
    public function destroyParty(Request $request, $job_id,$id)
    {
        $job = Job::findOrFail($job_id);
        $job_party = JobParty::findOrFail($id);
        $temp_name = $job_party->contact->full_name;
        $job_party->delete();

        // redirect
        Session::flash('message', 'Job party ' .$temp_name . ' successfully deleted.');
        
        return $request->from == 'stepedit' ? redirect()->to(route('client.research.edit', $job_id).'?#parties') :  redirect()->back();
    }

    public function additionalForm($party_type)
    {
        switch($party_type) {
            case 'customer':
                return;
                break;
            case 'general_contractor':
                return;
                break;
            case 'bond':
                return view('research.wizard.dynamicforms.bond');
                break;
            case 'landowner':
                return view('research.wizard.dynamicforms.landowner');
                break;
            case 'leaseholder':
                return view('research.wizard.dynamicforms.leaseholder');
                break;
            case 'copy_recipient':
                return view('research.wizard.dynamicforms.copy');
                return;
                break;
        }
    }
    public function listcontacts($id,Request $request)
    {
         $search_query = $request->input('term');
        
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

         $result=array();
         foreach ($all_contacts as $ac) {
             if ($ac['is_hot']==1 && $ac['use_on_client']==0){
              }
              else{
                array_push($result, $ac);
              }  
             
         }
         return json_encode($result);
    }




    public function uploadattachment($id,Request $request) {
      if ($request['file']==null || $request['file']=="" ) {
          Session::flash('message', 'file is required.');
          return redirect()->back();
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
          return redirect()->back();
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
               
          //}
      }
      if ($job->notify_email) {
        $notify_user = TempUser::create(['email'=>$job->notify_email]);
        Notification::send($notify_user, new NewAttachment($attachment->id,$data,$request->custom_message,Auth::user()->full_name,'job'));
        $notify_user->delete();
      }

      Session::flash('message', 'Attachment added');
      return $request->from=='research_edit' ? redirect()->to(route('client.research.edit',$id) .'?#attachments'):redirect()->back();
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
  
  
  public function destroy_attachment($id, Request $request) {
      $attachment = Attachment::findOrFail($id);
      $job_id = $attachment->attachable_id;
      if (is_null($attachment->thumb_path)) {
          
      } else {
          Storage::delete($attachment->thumb_path);
      }
      Storage::delete($attachment->file_path);
      $attachment->delete();
      
      Session::flash('message', 'Attachment removed');
      return $request->from=='research_edit' ? redirect()->to(route('client.research.edit',$job_id) .'?#attachments'):redirect()->back();
  }







    public function save_property(Request $request,$id){
        $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('client.jobs.edit', $id);
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
                    $landowner_deed_number='';
                    $data['source'] = 'OTHR';
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
        return redirect()->route('client.research.edit',$job->id);
        }
        $job->save();
        Session::flash('message', 'Property Search Failed.');
        return redirect()->route('client.research.edit',$job->id);

    }

    public function select_property(Request $request,$id){
      $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('client.jobs.edit', $id);
        }
      $res= json_decode($request['apiSearch_str']);
      $num=$request['property_number'];
      $api_property=$res->properties[$num];

            if (!isset($api_property->owners)){
                $job->search_status='No Address Found';
                $job->save();
                 
                Session::flash('message', 'No Address Found from API.');
                return redirect()->route('client.research.edit',$job->id);

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
        return redirect()->route('client.research.edit',$job->id);
    }

    public function runsearch($id)
    {
        $job=Job::where('id',$id)->first();
        if (count($job)==0){
          Session::flash('message', 'Job already deleted.');
          return redirect()->route('client.jobs.edit', $id);
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
              return redirect()->route('client.research.edit',$job->id);
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
                return redirect()->route('client.research.edit',$job->id);

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
              return redirect()->route('client.research.edit',$job->id);
            }

            $api_property=$res->properties[0];

            if (!isset($api_property->owners)){
                $job->search_status='No Address Found';
                $job->save();
                 
                Session::flash('message', 'No Address Found from API.');
                return redirect()->route('client.research.edit',$job->id);

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
        return redirect()->route('client.research.edit',$job->id);

         
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
        $workorder=WorkOrder::where('job_id',$id)->get();
        $len=count($workorder);

        if ($len>0){
            Session::flash('message', 'This job has one or more work orders.  Please delete all work orders before deleting this job.');

        }else{
            $job->delete();
            Session::flash('message', 'Job ' .$temp_name . ' successfully deleted.');
        }
        //return redirect()->to(($request->input('redirect_to')));
        return redirect()->route('client.jobs.edit', $id);
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

   
    
    public function setfilter (Request $request) {
        if ($request->has('county')) {
            if($request->county == '' ) {
                session()->forget('research_filter.county');
            } else {
                session(['research_filter.county' => $request->county]);
            }
        }

        session(['research_filter.has_corners' => 'all']);
        if ($request->has('has_corners')) {
            session(['research_filter.has_corners' => $request->has_corners]);
        }

        session(['research_filter.work_rush' => 'all']);
        if ($request->has('work_rush')) {
            session(['research_filter.work_rush' => $request->work_rush]);
        }

        return redirect()->route('client.jobs.edit', $id);
    }
    
    
    public function resetfilter (Request $request) {
        session()->forget('research_filter');
        return redirect()->route('client.jobs.edit', $id);
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
    
}
