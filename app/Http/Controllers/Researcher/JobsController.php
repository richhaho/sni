<?php

namespace App\Http\Controllers\Researcher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobLog;
use App\JobParty;
use App\WorkOrderType;
use App\WorkOrder;
use App\Client;
use Session; 
use DB;
use Auth;
use App\AttachmentType;
use App\Attachment;
use Response;
use Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAttachment;

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
            'none' => 'All',
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Demand For Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien',
            'closed' => 'Closed'
       ];
       
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
                 $jobs->where('status',session('job_filter.job_status'));
                
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
                $dates = explode(' - ',session('job_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $jobs->where([['started_at','>=',$from],['started_at','<=',$to]])->orderBy('started_at','desc');
            }
        }
       
       
       
       $jobs = $jobs->orderBy('id','DESC')->paginate(15);
       Session::put('backUrl',\URL::full());
       $data = [
           'jobs' => $jobs,
           'clients' => $clients,
           'job_statuses' => $job_statuses
       ];
         
       return view('researcher.jobs.index',$data);
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
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Demand For Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien',
            'closed' => 'Closed'
       ];
       $job_types = [
           'public' => 'Public - Roadwork, Airport, Government buildings etc',
           'private' => 'Private - Residential, Commercial properties etc',
       ];

       
       $data = [
         'clients' => $clients,
         'job_types'=>$job_types,
          'job_statuses' => $job_statuses,
          'address_sources' =>$address_sources
          
        ];
        return view('researcher.jobs.create',$data);
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
             'city' => 'required',
            'interest_rate' => 'numeric' ,
             'contract_amount' => 'numeric'
            
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
        $temp_name = $job->name;
        Session::flash('message', 'Job ' .$temp_name . ' created');
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
        return view('researcher.jobs.show',$data);
    }
    
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
            
            'notice-to-owner' => 'Notice to Owner',
            'release-of-lien' => 'Release of Lien',
            'demand-letter' => 'Demand Letter',
            'claim-of-lien' => 'Claim of Lien',
            'ammended-claim-of-lien' => 'Amended Claim of Lien',
            'notice-of-non-payment' => 'Demand For Payment',
            'partial-satisfaction-of-lien' => 'Partial Satisfaction of Lien',
            'satisfaction-of-lien' => 'Satisfaction of Lien',
            'closed' => 'Closed'
       ];
        $attachment_types= AttachmentType::get()->pluck('name','slug');
        $available_notices = [ 
            'amend-claim-of-lien',
            'claim-of-lien',
            'conditional-waiver-and-release-of-lien-upon-final-payment',
            'conditional-waiver-and-release-of-lien-upon-progress-payment',
            'contractors-final-payment-affidavit',
            'notice-of-bond',
            'notice-of-commencement',
            'notice-of-contest-of-claim-against-payment-bond',
            'notice-of-contest-of-lien',
            'notice-to-owner',
            'notice-of-non-payment',
            'notice-of-nonpayment-with-intent-to-lien-andor-foreclose',
            'partial-satisfaction-of-lien',
            'satisfaction-of-lien',
            'waiver-and-release-of-lien-upon-final-payment',
            'waiver-and-release-of-lien-upon-progress-payment',
            'waiver-of-right-to-claim-against-bond-final-payment',
            'waiver-of-right-to-claim-against-bond-progress-payment'
        ];
        $work_orders=WorkOrder::where('job_id',$id)->get();
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
            'work_orders'=>$work_orders
         ];
        return view('researcher.jobs.edit',$data);
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
            'client_id' => 'required',
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
        Session::flash('message', 'Job ' .$temp_name . ' updated.');
        if ($request->input('workorder') == '') {
            //return redirect()->to(($request->input('redirects_to')));
            return redirect()->route('jobs.edit',$job->id);
        } else {
            return redirect()->route('workorders.edit',$request->input('workorder'));
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


    public function listcontacts($id,Request $request)
    {
         $search_query = $request->input('term');
        
         $job = Job::findOrFail($id);
         $remove_contacts =$job->client->contacts->where('hot_id','<>',0)->pluck('hot_id')->toArray();
         $client_contacts = $job->client->contacts->pluck('id')->toArray();
         //dd($client);
         $entities= \App\Entity::search($search_query)->where('client_id',$job->client->id)->get()->pluck('id')->toArray();
         $entities_hot =  \App\Entity::search($search_query)->where('client_id',0)->get()->pluck('id')->toArray();
         $all_entities = array_merge($entities,$entities_hot);
         $entity_contacts=  \App\ContactInfo::whereIn('entity_id',$all_entities)->get();
        
         $contacts=  \App\ContactInfo::search($search_query)->get()->where('status',1)->whereIn('id',$client_contacts);
         $contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status',1)->where('is_hot',1)->whereNotIn('id',$remove_contacts);
         //$contacts_hot = \App\ContactInfo::search($search_query)->get()->where('status',1)->where('is_hot',1);
         $all_contacts = $contacts->merge($contacts_hot);
        
         
         $all_contacts = $all_contacts->merge($entity_contacts)->sortBy('name_entity_name')->toArray();
         $all_contacts = array_values($all_contacts);
        
         //$contacts=  \App\ContactInfo::search($search_query)->get()->whereIn('id',$client_contacts)->toArray();
         return json_encode($all_contacts);
         //$contacts=  \App\ContactInfo::search($search_query)->get()->whereIn('id',$client_contacts)->toArray();
         
    }
    
     public function listjobs($id,Request $request)
    {
        $search_query = $request->input('term');
        
        $clients = Client::search($search_query)->get()->pluck('id');
        //dd($clients);
        $client_jobs = Job::whereIn('client_id',$clients)->get();
        //dd($client_jobs);
        $jobs = Job::search($search_query )->get();
        $merge = $jobs->merge($client_jobs);
        $merge = $merge->whereNotIn('id',[$id]);
        $all_contacts =$merge->toJson();
        //$contacts=  \App\ContactInfo::search($search_query)->get()->whereIn('id',$client_contacts)->toArray();
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
        
        return view('researcher.jobs.components.copydata',$data);
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
                                ->where('last_name',$contact->last_name)->count();
                        if ( $contact_count > 0) {
                            // use existent contact and existent entity to recreate job party
                            $existent_contact = \App\ContactInfo::where('entity_id',$existent_entity->id)
                                ->where('first_name',$contact->first_name)
                                ->where('last_name',$contact->last_name)->first();
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
                    $new_attachment->save();
                    
                    $new_path = 'attachments/jobs/' . $id. '/attachment-' . $new_attachment->id . substr($new_attachment->file_path,-4);
                    $new_path_thumb = 'attachments/jobs/' . $id. '/thumbnail-' . $new_attachment->id . substr($new_attachment->thumb_path,-4);
                    
                    Storage::copy($new_attachment->file_path, $new_path);
                    Storage::copy($new_attachment->thumb_path, $new_path_thumb);
                    
                    $new_attachment->file_path = $new_path;
                    $new_attachment->thumb_path = $new_path_thumb;
                    $new_attachment->save();
                }
             
         }
        
        if($changes) {
            Session::flash('message', 'Info selected succesfully copied to current job');
        }
        
        return redirect()->route('jobs.edit',$id);
    }


    public function uploadattachment($id,Request $request) {
        $job = Job::findOrFail($id);
        $client =$job->client;
        $this->validate($request, [
            'file' => 'required|file',   
        ]);
        
        $attachment = new Attachment();
        $f = $request->file('file');
        
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
        $attachment->save();
        
        
        if ($request->has('notify')) {
            $data = [
                'note' => 'Have been added to a Job',
                'entered_at' => $attachment->created_at->format('Y-m-d H:i:s')
            ];
            Notification::send($client->users, new NewAttachment($attachment->id,$data,Auth::user()->full_name,'job'));
        }
        Session::flash('message', 'Attachment added');
        return redirect()->route('jobs.edit',['id'=>$id,'#attachments']);
    }
    
    
    public function showattachment($job_id,$id) {
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
    
    public function getLastDay ($id) {
        $job = Job::findOrFail($id);
        return $job->last_day->format('m/d/Y');
    }
}
