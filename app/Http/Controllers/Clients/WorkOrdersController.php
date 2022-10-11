<?php

namespace App\Http\Controllers\Clients;

use App\Notifications\NewWorkOrder;
use App\Attachment;
use App\AttachmentType;
use App\BatchDetail;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobParty;
use App\ContactInfo;
use App\WorkOrder;
use App\WorkOrderType;
use App\Todo;
use App\TodoDocument;
use App\TodoInstruction;
use Auth;
use Illuminate\Http\Request;
use Imagick;
use Response;
use Session;
use Storage;
use App\Template;
use App\Invoice;
use App\InvoiceLine;
use App\Client;
use App\Notifications\WorkOrderCancelled;
use App\Notifications\TodoItemCompleted;
use App\User;
use App\TempUser;
use Illuminate\Support\Facades\Notification;
use App\WorkOrderFields;
use App\WorkOrderAnswers;
use App\CompanySetting;
use App\Notifications\NewAttachment;
use App\Payment;
use App\Custom\Payeezy;
use Mail;
use App\Mail\PaymentMade;
use Carbon\Carbon;

class WorkOrdersController extends Controller
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
        $this->wo_types = WorkOrderType::all()->pluck('name','slug')->sortBy('name')->toArray();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
       $works = WorkOrder::query();
       
       $works->whereHas('job',function($q) {
            return $q->where('client_id',Auth::user()->client_id);
        });
        $jobs_list = Job::where('client_id',Auth::user()->client_id)->get()->sortby('name')->pluck('name','id')->prepend('All',0);
        
       
       if (session()->has('work_order_filter.term')) {
            if(session('work_order_filter.term') != '') {
                 $xjobs = Job::search(session('work_order_filter.term'))->where('client_id',Auth::user()->client_id)->get()->pluck('id');
                 $xwoj = WorkOrder::whereIn('job_id',$xjobs)->get();
                 
                 $xcontact = ContactInfo::where('deleted_at', null);
                 $xcontact =  ContactInfo::scopeSearchByKeyword($xcontact, session('work_order_filter.term'))->get()->pluck('id');
                 $parties = JobParty::whereIn('contact_id',$xcontact)->get()->pluck('job_id')->unique();
                 $xwop = WorkOrder::whereIn('job_id',$parties)->get();
                 if ($xwoj->isEmpty()){
                     if ($xwop->isEmpty()){
                         $xwom = [];
                     } else {
                         $xwom = $xwop;
                     }
                 } else {
                      if ($xwop->isEmpty()){
                         $xwom = $xwoj;
                     } else {
                         $xwom = $xwoj->merge($xwop)->unique();
                     }
                 }
                 if (count($xwom) > 0) {
                      $xids = $xwom->pluck('id');
                      $works->whereIn('id',$xids);
                 } else {
                      $works->where('id', 0);
                 }
                 
            }
       }
       
       if (session()->has('work_order_filter.job')) {
           $xjob = session('work_order_filter.job');
           if ($jobs_list->contains(function($value,$key) use ($xjob){ return $key == $xjob; })) {
               $works->where('job_id',session('work_order_filter.job'));
               $jobs_list = [ $xjob => $jobs_list[$xjob]];
           } else {
               session()->forget('work_order_filter.job');
               $jobs_list = [0=>'All'];
           }
           
       } else {
           $jobs_list = [0=>'All'];
       }
       
       
       if (session()->has('work_order_filter.work_type')) {
           if(session('work_order_filter.work_type') != 'all'){
               $works->where('type',session('work_order_filter.work_type'));
           }
       }
       
       if (session()->has('work_order_filter.work_rush')) {
           if(session('work_order_filter.work_rush') != 'all'){
               $works->where('is_rush',session('work_order_filter.work_rush'));
           }
       }
       
       if (session()->has('work_order_filter.work_status')) {
           if(session('work_order_filter.work_status') != 'all'){
               $works->where('status',session('work_order_filter.work_status'));
           } else {
                $works->where('status', '!=' , 'temporary');
           }
       } else {
            $works->where('status', '!=' , 'temporary');
       }
       
       if (session()->has('work_order_filter.daterange')) {
            if(session('work_order_filter.daterange') != '') {
                $date_range=session('work_order_filter.daterange');
                $date_type="/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/";
                
               if (preg_match($date_type,$date_range)){
                $dates = explode(' - ',session('work_order_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                 
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $works->where([['due_at','>=',$from],['due_at','<=',$to]])->orderBy('due_at','desc');
                Session::flash('message', null);
                 }else{
                    Session::flash('message', 'Input not in expected range format');
                 }
            }
        }
        
        
        if (session()->has('work_order_filter.work_condition')) {
           switch (session('work_order_filter.work_condition')) {
               case 1:
                    $works->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit']);
                    break;
               case 2:
                   $works->whereIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit']);
                    break;
               default:
           }
       } else {
           session(['work_order_filter.work_condition' => 0]);
            //$works->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge']);
       }

        if (session()->has('work_order_filter.work_number')) {
            if(session('work_order_filter.work_number') != '') {
                $works->where('id',session('work_order_filter.work_number'));
            }
        }

        if (session()->has('work_order_filter.job_number')) {
            if(session('work_order_filter.job_number') != '') {
                $xjob_number = session('work_order_filter.job_number');
                $works->whereHas('job',function($q) use ($xjob_number) {
                    return $q->where('number','like','%' . $xjob_number . '%');
                });
            }
        }
        if (session()->has('work_order_filter.job_address')) {
            if(session('work_order_filter.job_address') != '') {
                $xjob_address = session('work_order_filter.job_address');
                $works->whereHas('job',function($q) use ($xjob_address) {
                    return $q->where('address_1','like','%' . $xjob_address . '%')->orwhere('address_2','like','%' . $xjob_address . '%')->orwhere('city','like','%' . $xjob_address . '%')->orwhere('zip','like','%' . $xjob_address . '%');
                });
            }
        }

        if (session()->has('work_order_filter.job_county')) {
            if(session('work_order_filter.job_county') != '') {
                $xjob_county = session('work_order_filter.job_county');
                $works->whereHas('job',function($q) use ($xjob_county) {
                    return $q->where('county','like','%' . $xjob_county . '%');
                });
            }
        }

        if (session()->has('work_order_filter.customer_name')) {
            if(session('work_order_filter.customer_name') != '') {
                $xcustomer_name = session('work_order_filter.customer_name');
                $job_ids=JobParty::where('type','customer')->whereHas('firm',function($p) use ($xcustomer_name) {
                    return $p->where('firm_name', $xcustomer_name);
                })->pluck('job_id')->toArray();
                
                $works->whereHas('job',function($j) use ($job_ids) {
                    return $j->whereIn('id',$job_ids);
                });
            }
        }


       //$works_id= $works->pluck('id');
       
       $works = $works->orderBy('id','DESC')->paginate(15);       
      
       Session::put('backUrl',\URL::full());

       $client_id=Auth::user()->client_id;
       $customers=JobParty::where('type','customer')
                        ->whereHas('firm', function($f) use ($client_id) {
                            return $f->where('client_id',$client_id)
                                     ->where('firm_name','!=',null);
                         })
                        ->with('firm')->get()
                        ->pluck('firm.firm_name','firm.firm_name')
                        ->prepend('All','')->toArray();
        
        ksort($customers);
       
        $conditions = [
           '0' => 'All',
           '1' => 'Open',
           '2' => 'Close',
       ];
       $data = [
           'conditions' => $conditions,
           'works' => $works,
           'wo_types' => ['all' => 'All'] + $this->wo_types,
           'statuses' => ['all' => 'All'] + $this->statuses,
           'jobs' => $jobs_list,
           'customers' => $customers,
       ];
       //echo  json_encode($data);
         
       return view('client.workorders.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $job_id = '';
        if ($request->has('job_id')) {
           $job_id = $request->input('job_id');
           session(['work_order_filter.job'=>$request->input('job_id')]);
       } else {
            if (session()->has('work_order_filter.job')) {       
                $job_id = session('work_order_filter.job');
            } else {
                $job_id = '';
            }
       }
        $jobs_list = Job::all()->pluck('name','id')->toArray();
       
       
        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'job_id' => $job_id
        ];
        
        
        
        return view('client.workorders.create',$data);
    }

    public function getfields(Request $request)
    {
        if (!$request['work_order_id']){
            $question_list=WorkOrderFields::where('workorder_type',$request->work_order_type)->orderBy('field_order')->get();
            return response()->json($question_list);
        }else{
            $work = WorkOrder::findOrFail($request['work_order_id']);
            $answer_list=WorkOrderAnswers::where('work_order_id',$request['work_order_id'])->pluck('answer','work_order_field_id');
            $question_list=WorkOrderFields::where('workorder_type',$request->work_order_type)->where('created_at','<',$work->created_at)->orderBy('field_order')->get();
           
            $data['answer']=$answer_list;
            $data['question']=$question_list;

            return response()->json($data);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $messages = [
            'date_format'    => 'All dates must comply wit the folowing format mm/dd/yyyy',
        ];

         $this->validate($request, [
            'due_at' => 'required|date|date_format:m/d/Y',
            'job_id' => 'required|exists:jobs,id',
            'type' => 'required',
            
         ],$messages); 
        $data = $request->all();
        $job = Job::findOrFail($request->job_id);
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
       if($job->client->billing_type == "invoiced") {
                $data['status'] = 'open';
            } else {
                 $data['status'] = 'payment pending';
            }
        
        $wo =  WorkOrder::create($data);
        $user_id = Auth::user()->id;
        $wo->created_by = $user_id;
        $wo->responsible_user = $user_id;
        $wo->service = $job->client->service;
        $wo->save();
        
        $ndata = [
            'note' => $wo->number,
            'entered_at' =>  $wo->created_at->format('Y-m-d H:i:s')
        ];

        $adminEmail = \App\AdminEmails::where('class', 'NewWorkOrder')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status',1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status',1)->isRole(['admin','researcher'])->get();
        } 
        Notification::send( $admin_users, new NewWorkOrder($wo->id,$ndata,Auth::user()->full_name));


        if ($request->input('answer')){
            foreach ($request->input('answer') as $key => $answer)
            {
                $answer_data=[
                    'work_order_id'=>$wo->id,
                    'work_order_field_id'=>$key,
                    'answer'=>$request->answer[$key],
                ];

                $answer=WorkOrderAnswers::create($answer_data);

            }
        }
        
        Session::flash('message', 'Work Order ' .$wo->number . ' created');
        return redirect()->route('client.notices.edit',$wo->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $work = WorkOrder::findOrFail($id);
       
        $attachments = $work->attachments->where('type','generated');
        //dd($attachments);
        if (count($attachments)>0) {
            $data = [
                'id' => $id,
                 'attachments' => $attachments
            ];
            return view('client.workorders.show',$data);
        } else {
            return redirect()->back();
        }
    }
    
    
    public function view($work_order_id,$id)
    {
         $attachment = Attachment::findOrFail($id);
         //dd($attachment->file_path);
         $content = Storage::get($attachment->file_path);

        return Response::make($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $attachment->original_filename . '"'
        ]);
    }
    
    
     /**
     * Display the document.
     *
     * @param  int  $id
     * @return Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
         
        $existsWorkorder=WorkOrder::where('id',$id)->get();
        if (count($existsWorkorder)<1){
            Session::flash('message', 'Work Order 00000' .$id . ' has been deleted already.');
            return redirect()->route('client.notices.index');
        }

        

        if (session()->has('note')) {
            $xnote = session('note');
        } else {
            $xnote = "";
        }
        $work = WorkOrder::findOrFail($id);

        if($work->job->client_id <> Auth::user()->client->id) {
            abort(403);
        }
        
        //$jobs_list = Job::where('client_id',Auth::user()->client_id)->pluck('name','id')->prepend('All',0);
        $jobs_list = Job::where('client_id',Auth::user()->client_id)->get()->pluck('name','id')->toArray();
        if ($work->job->status=='closed'){
            $jobs_list[$work->job->id]=$work->job->name;
        }

        $attachment_types= AttachmentType::where('slug','!=','generated')->get()->pluck('name','slug');

        $question_list=WorkOrderFields::where('workorder_type',$work->type)->where('created_at','<',$work->created_at)->orderBy('field_order')->get();
        $answer_list=WorkOrderAnswers::where('work_order_id',$work->id)->pluck('answer','work_order_field_id');

        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'statuses' => $this->statuses,
            'work' => $work,
            'attachment_types' => $attachment_types,
            'parties_type' => $this->parties_type,
            'question_list' => $question_list,
            'answer_list' => $answer_list,
            'xnote' => $xnote
        ];
        //echo json_encode($work->attachments[0]->recipient);

        return view('client.workorders.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'due_at' => 'required|date|date_format:m/d/Y',
        //     'job_id' => 'required|exists:jobs,id',
        //     'type' => 'required',

        // ]); 
        $wo =  WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $wo->job);
        $data = $request->all();
        
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
        $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));
        // $wo->update($data);


        $question_list=WorkOrderFields::where('workorder_type',$wo->type)->orderBy('field_order')->get();
        $answer_list=WorkOrderAnswers::where('work_order_id',$wo->id)->pluck('answer','work_order_field_id');

        if ($request->input('answer')){
            
            
                foreach ($request->input('answer') as $key => $answer)
                {
                    $answer_data=[
                        'work_order_id'=>$wo->id,
                        'work_order_field_id'=>$key,
                        'answer'=>$request->answer[$key],
                    ];
                    if (isset($answer_list[$key])){
                        $answer=WorkOrderAnswers::where('work_order_id',$wo->id)->where('work_order_field_id',$key)->first();
                        $answer->update($answer_data);

                    }else{
                        $answer=WorkOrderAnswers::create($answer_data);
                    }

                }
             
        }
        
        Session::flash('message', 'Work Order ' .$wo->number . ' updated');
        return redirect()->route('client.notices.edit',$wo->id);
        
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $work = WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $work->job);
        $temp_name = $work->number;
        $work->delete();

        // redirect
        Session::flash('message', 'Work Order  ' .$temp_name . ' successfully deleted.');
        
        return redirect()->route('client.notices.index');
    }
    
    
     public function uploadattachment($id,Request $request) {
        if ($request['file']==null || $request['file']=="" ) {
            Session::flash('message', 'file is required.');
            return redirect()->route('client.notices.edit',['id'=>$id,'#attachments']);

        }

        $work = WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $work->job);
        // $this->validate($request, [
        //     'file' => 'required|file',   
        // ]);
        
        $attachment = new Attachment();
        $f = $request->file('file');

        $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
         
        if ($f->getSize()>$max_uploadfileSize){
            Session::flash('message', 'This file is too large to upload.');
            return redirect()->route('client.notices.edit',['id'=>$id,'#attachments']);
        }        
        
        $attachment->type = $request->input('type');
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $work->attachments()->save($attachment);
        $attachment->save();
         
        $xfilename = "attachment-" .$attachment->id . "." . $f->guessExtension();
        $xpath = 'attachments/workorders/' . $id . '/';
        $f->storeAs($xpath,$xfilename);
        $attachment->file_path = $xpath .  $xfilename;
        $attachment->save();
        
        
        //dd($f->getMimeType());
        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new Imagick();
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
                $img = new Imagick();
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
        $adminEmail = \App\AdminEmails::where('class', 'NewAttachment')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status',1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status',1)->isRole(['admin','researcher'])->get();
        } 
        $data = [
                'note' => 'Have been added to a Notice',
                'entered_at' =>  $attachment->created_at->format('Y-m-d H:i:s')
        ];
        Notification::send($admin_users, new NewAttachment($attachment->id,$data,'',Auth::user()->full_name,'notice'));

        $job = $work->job;
        if ($job->notify_email) {
            $notify_user = TempUser::create(['email'=>$job->notify_email]);
            Notification::send($notify_user, new NewAttachment($attachment->id,$data,'',Auth::user()->full_name,'job'));
            $notify_user->delete();
        }
        
        Session::flash('message', 'Attachment added');
        return redirect()->route('client.notices.edit',['id'=>$id,'#attachments']);
    }
    
     public function downloadattachment($job_id,$id) {
        if (count(Attachment::where('id',$id)->get())<1){
            Session::flash('message', 'File no longer exists.');
            return redirect()->route('client.notices.edit',$job_id);
        }
        $attachment = Attachment::findOrFail($id);;
        return response()->download(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $attachment->file_path);
//        $contents = Storage::get($job_party->bond_pdf);
//        $response = Response::make($contents, '200');
//        $response->header('Content-Type', $job_party->bond_pdf_filename_mime);
//        return $response;
        
    }
    
    public function showattachment($job_id,$id) {
        if (count(Attachment::where('id',$id)->get())<1){
            Session::flash('message', 'File no longer exists.');
            return redirect()->route('client.notices.edit',$job_id);
        }
        $attachment = Attachment::findOrFail($id);
        $contents = Storage::get($attachment->file_path);
        $response = Response::make($contents, '200');
        $response->header('Content-Type', $attachment->file_mime);
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
    
    
     public function cancel($id) {
        $work =  WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $work->job);

        if ($work->service=='self') {
            $recipients_ids = $work->recipients()->pluck('id')->toArray();;
            $batches_ids=BatchDetail::whereIn('work_order_recipient',$recipients_ids)->orderBy('batch_id')->get()->pluck('batch_id')->toArray();
            $batches_ids=array_values(array_unique($batches_ids));
            if (count($batches_ids)>0){
                Session::flash('message', 'One or more of these PDFs have been used in the below batches. You must first delete these batches before you can delete the PDFs. Please Remove Batch number '.json_encode($batches_ids));
                return redirect()->back();
            }
            $attachments  = $work->attachments()->where('type','generated')->get();
            $recipients = $work->recipients;
            foreach ($work->pdf_pages as $xpage) {
                $xpage->delete();
            } 
            foreach ($attachments as $xattachment) {
                $xattachment->delete();
            } 
            foreach ($recipients as $xrecipient) {
                $xrecipient->delete();
            }
        }
        
        $adminEmail = \App\AdminEmails::where('class', 'WorkOrderCancelled')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status',1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status',1)->isRole(['admin','researcher'])->get();
        } 
        Notification::send( $admin_users, new WorkOrderCancelled($work,Auth::user(),'admin'));

        $client_users = Auth::user()->client->users->where('deleted_at',null);
        Notification::send( $client_users, new WorkOrderCancelled($work,Auth::user(),'client'));
         
        $work->status = "cancelled";
        $work->save();
          
        Session::flash('message', 'Work Order  ' .$work->name . ' successfully cancelled.');
        
        return redirect()->route('client.notices.index');
         
     }
    
    public function destroy_attachment($id) {
        $attachment = Attachment::findOrFail($id);
        $work_id = $attachment->attachable_id;
        $work = WorkOrder::findOrFail($work_id);
        $this->authorize('wizard',  $work->job);
        if (is_null($attachment->thumb_path)) {
            
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();
        
        Session::flash('message', 'Attachment removed');
        return redirect()->route('client.notices.edit',['id'=>$work_id,'#attachments']);
    }
    
    public function setfilter (Request $request) {
        if ($request->has('resetfilter')) {
               if($request->resetfilter=='true') {
                   session()->forget('work_order_filter');
               }
        }
        
        
        if ($request->has('term_filter')) {
            if(strlen($request->term_filter) == 0 ) {
                session()->forget('work_order_filter.term');
            } else {
                session(['work_order_filter.term' => $request->term_filter]);
            }
        }
        
        
        if ($request->has('job_filter')) {
            if($request->job_filter == 0 ) {
                session()->forget('work_order_filter.job');
            } else {
                session(['work_order_filter.job' => $request->job_filter]);
            }
        }
        
         if ($request->has('work_type')) {
            if($request->work_type == "all" ) {
                session()->forget('work_order_filter.work_type');
            } else {
                session(['work_order_filter.work_type' => $request->work_type]);
            }
        }
        
        if ($request->has('work_rush')) {
            if($request->work_rush == "all" ) {
                session()->forget('work_order_filter.work_rush');
            } else {
                session(['work_order_filter.work_rush' => $request->work_rush]);
            }
        }
        
        if ($request->has('work_status')) {
            if($request->work_status == "all" ) {
                session()->forget('work_order_filter.work_status');
            } else {
                session(['work_order_filter.work_status' => $request->work_status]);
            }
        }
        
         if ($request->has('work_condition')) {

                session(['work_order_filter.work_condition' => $request->work_condition]);
          
        }
        
        if ($request->has('daterange')) {
            if($request->daterange == '' ) {
                session()->forget('work_order_filter.daterange');
            } else {
                session(['work_order_filter.daterange' => $request->daterange]);
            }
         }


         if ($request->has('job_number')) {
            if($request->job_number == '' ) {
                session()->forget('work_order_filter.job_number');
            } else {
                session(['work_order_filter.job_number' => $request->job_number]);
            }
         }
         if ($request->has('job_address')) {
            if($request->job_address == '' ) {
                session()->forget('work_order_filter.job_address');
            } else {
                session(['work_order_filter.job_address' => $request->job_address]);
            }
         }
         if ($request->has('job_county')) {
            if($request->job_county == '' ) {
                session()->forget('work_order_filter.job_county');
            } else {
                session(['work_order_filter.job_county' => $request->job_county]);
            }
         }

         if ($request->has('customer_name')) {
            if($request->customer_name == '' ) {
                session()->forget('work_order_filter.customer_name');
            } else {
                session(['work_order_filter.customer_name' => $request->customer_name]);
            }
         }else{
            session()->forget('work_order_filter.customer_name');
         }

         if ($request->has('work_number')) {
            if($request->work_number == '' ) {
                session()->forget('work_order_filter.work_number');
            } else {
                session(['work_order_filter.work_number' => $request->work_number]);
            }
         }
        return redirect()->route('client.notices.index');
    }
    
    
    public function resetfilter (Request $request) {
         session()->forget('work_order_filter');
        return redirect()->route('client.notices.index');
    }

    // Request Additional Service page
    public function requestService($id) {
        $work =  WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $work->job);
        $client =  $work->job->client;
        $template = Template::where('type_slug',$work->type)->where('client_id',$client->id)->first();
        if(empty($template)) {
            $template = Template::where('type_slug',$work->type)->where('client_id',0)->first();
        }
        $templateLines = [];
        if ($template) {
            $templateLines = $template->lines->where('type', 'additional-service');
        }
        $data = [
            'work' => $work,
            'templateLines' => $templateLines
        ];
        return view('client.workorders.additionalservice.request',$data);
    }

    // Purchase Additional-Services
    public function purchaseService($id, Request $request) {
        $this->validate($request, [
            'choiceTodos' => 'required',
        ]); 
        $work =  WorkOrder::findOrFail($id);
        $this->authorize('wizard',  $work->job);
        $client =  $work->job->client;
        $data = $request->all();
        $total_amount = 0;
        foreach($data['choiceTodos'] as $key => $val) {
            $total_amount+=$data['price'][$key];
        }
        if (!$total_amount) {
            Session::flash('message', 'Total amount must be more than 0.');
            return redirect()->back();
        }
        
        $invoice = new Invoice();
        $invoice->client_id  = $client->id;
        $invoice->work_order_id  = $work->id;
        $invoice->type = "additional-service";
        $invoice->due_at = \Carbon\Carbon::now();
        $invoice->status = "open";
        $invoice->total_amount = $total_amount;
        $invoice->save();
        foreach($data['choiceTodos'] as $key => $val) {
            $line = New InvoiceLine();
            $line->invoice_id = $invoice->id;
            $line->description = $data['description'][$key];
            $line->quantity = $data['quantity'][$key];
            $line->price = $data['price_item'][$key];
            $line->amount = $data['price'][$key];
            $line->status = "";
            $line->save();
            
            $todo = new Todo();
            $todo->workorder_id = $work->id;
            $todo->invoice_id = $invoice->id;
            $todo->invoice_line_id = $line->id;
            $todo->template_id = $key;
            $todo->name = $data['todo_name'][$key];
            $todo->description = $data['description'][$key];
            $todo->summary = $data['summary'][$key];
            $todo->todo_uploads = $data['todo_uploads'][$key];
            $todo->todo_instructions = $data['todo_instructions'][$key];
            $todo->status = 'pending';
            $todo->save();
            $instruction = isset($data['instruction'][$key]) ? $data['instruction'][$key] : null;
            $this->enterInstruction($todo, $instruction);
            $file = isset($request->file('upload')[$key]) ? $request->file('upload')[$key] : null;
            $upload_description = isset($data['upload_description'][$key]) ? $data['upload_description'][$key] : '';
            $this->uploadFile($todo, $file, $upload_description);
        }
        
        $company =  CompanySetting::first();
        $data = [
            'work' => $work,
            'client' => $client,
            'invoice' => $invoice,
            'api_key' => $company->apikey,
            'api_secret' => $company->apisecret,
            'js_security_key' => $company->js_security_key,
            'ta_token' => $company->ta_token,
            'payeezy_url' => $company->url
        ];
        return view('client.workorders.additionalservice.payment',$data);
    }

    public function uploadFile($todo, $f, $upload_description) {
        if (!$f) return;
        $id = $todo->id;
        $attachment = new TodoDocument();
        $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
         
        if ($f->getSize()>$max_uploadfileSize){
            Session::flash('message', 'This file is too large to upload.');
            return redirect()->back();
        }        
        
        $attachment->description = $upload_description;
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $attachment->todo_id = $todo->id;
        $attachment->save();
         
        $xfilename = "attachment-" .$attachment->id . "." . $f->guessExtension();
        $xpath = 'attachments/workorders/todos/' . $id . '/';
        $f->storeAs($xpath,$xfilename);
        $attachment->file_path = $xpath .  $xfilename;
        $attachment->save();

        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new Imagick();
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
                $img = new Imagick();
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
    }

    public function enterInstruction($todo, $text) {
        if (!$text) return;
        $instruction = new TodoInstruction();
        $instruction->instruction = $text;
        $instruction->entered_by = Auth::user()->id;
        $instruction->todo_id = $todo->id;
        $instruction->created_at = Carbon::now();
        $instruction->viewable = 1;
        $instruction->save();    
    }

    public function payService($id, Request $request) {
        if ($request->has('donottokenize')) {
            $this->validate($request, [
                'invoice_id' => 'required',
                'currency' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'invoice_id' => 'required',
                'currency' => 'required',
                'token' => 'required',
            ]);  
        }
        $invoice = Invoice::findOrFail($request->invoice_id);
        $this->authorize('capturecc',  $invoice);
        $data = $request->all();
        $data['token'] = json_decode($request->token,true);
        
        $client = $invoice->client;
        if(strlen($client->payeezy_type)==0) {
            $client->payeezy_type = $data['token']['type'];
            $client->payeezy_value = $data['token']['value'];
            $client->payeezy_cardholder_name = $data['token']['cardholder_name'];
            $client->payeezy_exp_date = $data['token']['exp_date'];
            $client->save();
        }
        $company =  CompanySetting::first();
        
        $py = new Payeezy();
        $py->setApiKey($data['apikey']);
        $py->setApiSecret($data['apisecret']);
        $py->setMerchantToken($company->merchant_token);
        $py->setUrl('https://' . $company->url . '/v1/transactions');
        if ($client->company_name=="" || $client->company_name==null){
            $client_name=$client->first_name." ".$client->last_name;
        }else{
            $client_name=$client->company_name;
        }
        
        $payload = [
            'merchant_ref' =>  $client_name,
            'transaction_type'=> 'purchase',
            'method'=> 'token',
            'amount'=> number_format($invoice->total_amount,2,'',''),
                'currency_code'=> $data['currency'],
                'token'=> [
                    'token_type'=> 'FDToken',
                    'token_data'=> [
                        'type' =>  $client->payeezy_type,
                        'value' => $client->payeezy_value,
                        'cardholder_name' => $client->payeezy_cardholder_name,
                        'exp_date' => $client->payeezy_exp_date
                    ]
                ]
        ];
        
        $result = $py->purchase($payload);
        $result_data = json_decode($result);

        $payment = new Payment();
        $payment->invoices_id = serialize([$invoice->id]);
        $payment->type = 'credit_card';
        $payment->amount = $invoice->total_amount;
        $payment->client_id = $client->id;
        $payment->reference = $result_data->correlation_id;
        $payment->gateway = 'payeezy';
        $payment->transaction_status = $result_data->transaction_status;
        $payment->log_result = $result;
        $payment->user_id = Auth::user()->id;
        $payment->save();
      
        // change invoice status
        if ( $result_data->transaction_status == "approved") {
            $users = Auth::user()->client->activeusers;
            foreach ($users as $user) {
                $mailto [] = $user->email;
            }
            $invoice->status ="paid";
            $invoice->payment_id =$payment->id;
            $invoice->payed_at = \Carbon\Carbon::now();
            $invoice->save();
            foreach ($invoice->todos() as $todo) {
                $todo->status = 'paid';
                $todo->save();
            }
            $work = $invoice->work_order;
            if (count($work->incompleteTodos())) {
                $work->has_todo = 1;
                $work->save();
            }
            $client=Auth::user()->client;
            if(json_encode(unserialize($client->override_payment))!="false" && json_encode(unserialize($client->override_payment))!="null"){
                Mail::to(unserialize($client->override_payment))->send(new PaymentMade($invoice->total_amount, [$invoice],$client,$payment->created_at));
            }else{
                $mailto = array();
                $responsible_user = User::where('id', $invoice->work_order->responsible_user)->first();
                if ($invoice->work_order->responsible_user && count($responsible_user)>0) {
                    $mailto [] = $responsible_user->email;
                } else {
                    $users = $work->job->client->activeusers;
                    foreach ($users as $user) {
                        $mailto [] = $user->email;
                    }
                }
                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new PaymentMade($invoice->total_amount, [$invoice],$client,$payment->created_at));
                }
            }
            if ($work->job->notify_email) {
                Mail::to($work->job->notify_email)->send(new PaymentMade($invoice->total_amount, [$invoice],$client,$payment->created_at));
            }
        } else {
            $invoice->status ="unpaid";
            $invoice->save();
        }
       
       
        return json_encode([
            'status' => $invoice->status,
            'id' => $payment->id
        ]);
        
    }

    public function paid($id, $payment_id) {
        $payment = Payment::findOrFail($payment_id);
        $invoices_id = unserialize($payment->invoices_id);
        $invoice = Invoice::findorFail($invoices_id[0]);
        $data = [
            'payment' => $payment,
            'invoice' => $invoice,
            'work_id'  =>$id
        ];
        return view('client.workorders.additionalservice.paid',$data);
    }
    
    public function unpaid($id, $payment_id) {
        $payment = Payment::findOrFail($payment_id);
        $invoices_id = unserialize($payment->invoices_id);
        $invoice = Invoice::findorFail($invoices_id[0]);
        $data = [
            'work_id' => $id
        ];
        return view('client.workorders.additionalservice.unpaid',$data);
    }

    public function todoEdit($work_id, $id) {
        $todo = Todo::findOrFail($id);
        $work = WorkOrder::findOrFail($work_id);
        $data = [
            'work' => $work,
            'todo' => $todo
        ];
        return view('client.workorders.additionalservice.todo',$data);
    }

    public function todoComplete($work_id, $id) {
        $todo = Todo::findOrFail($id);
        $todo->status = 'completed';
        $todo->completed_at = Carbon::now();
        $todo->save();
        $work = $todo->workorder();
        if (count($work->incompleteTodos()) == 0) {
            $work->has_todo = 0;
            $work->save();
        }
        $client_users = Auth::user()->client->activeusers;
        Notification::send( $client_users, new TodoItemCompleted($todo->workorder(),Auth::user(),$todo));
        return redirect()->to(url()->previous().'?#todos');
    }

    public function todoUpload($id,Request $request) {
        if ($request['file']==null || $request['file']=="" ) {
            Session::flash('message', 'file is required.');
            return redirect()->back();
        }
        $todo = Todo::findOrFail($id);
        
        $attachment = new TodoDocument();
        $f = $request->file('file');

        $max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
        $max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
         
        if ($f->getSize()>$max_uploadfileSize){
            Session::flash('message', 'This file is too large to upload.');
            return redirect()->back();
        }        
        
        $attachment->description = $request->input('description');
        $attachment->original_name = $f->getClientOriginalName();
        $attachment->file_mime = $f->getMimeType();
        $attachment->file_size = $f->getSize();
        $attachment->user_id = Auth::user()->id;
        $attachment->todo_id = $todo->id;
        $attachment->save();
         
        $xfilename = "attachment-" .$attachment->id . "." . $f->guessExtension();
        $xpath = 'attachments/workorders/todos/' . $id . '/';
        $f->storeAs($xpath,$xfilename);
        $attachment->file_path = $xpath .  $xfilename;
        $attachment->save();

        switch ($f->getMimeType()) {
            case 'application/pdf':
                $xblob = file_get_contents($f->getRealPath());
                $img = new Imagick();
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
                $img = new Imagick();
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

        Session::flash('message', 'To Do Document uploaded');
        return redirect()->back();
    }

    public function destroyTodoDocument($id) {
        $attachment = TodoDocument::findOrFail($id);
        if (is_null($attachment->thumb_path)) {
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();
        Session::flash('message', 'ToDo document removed');
        return redirect()->back();
    }

    public function downloadTodoDocument($id) {
        if (count(TodoDocument::where('id',$id)->get())<1){
            Session::flash('message', 'File no longer exists.');
            return redirect()->back();
        }
        $attachment = TodoDocument::findOrFail($id);;
        return response()->download(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $attachment->file_path);
    }

    public function showTodoThumbnail($id) {
        $attachment = TodoDocument::findOrFail($id);
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

    public function todoInstruction($id,Request $request) {
        if (!$request['instruction']) {
            Session::flash('message', 'file is required.');
            return redirect()->back();
        }
        $todo = Todo::findOrFail($id);
        
        $instruction = new TodoInstruction();
                
        $instruction->instruction = $request->input('instruction');
        $instruction->entered_by = Auth::user()->id;
        $instruction->todo_id = $todo->id;
        $instruction->created_at = Carbon::now();
        $instruction->viewable = 1;
        $instruction->save();
        Session::flash('message', 'To Do Instruction was added.');
        return redirect()->back();
    }

    public function destroyTodoInstruction($id) {
        $instruction = TodoInstruction::findOrFail($id);
        $instruction->delete();
        Session::flash('message', 'ToDo instruction removed');
        return redirect()->back();
    }

    public function existingWorkorderUnpaid(Request $request) {
        $job_id = $request->job_id;
        $work_type = $request->work_type;
        $client = Auth::user()->client;
        $data = [
            'work_id' => null,
            'invoice_id' => null,
        ];
        if (!isset($job_id) || !isset($work_type)) return response()->json($data);
        $tempWork = WorkOrder::where('job_id', $job_id)->where('type', $work_type)->where('status', 'temporary')->orderBy('created_at', 'desc')->first();
        $unpaidWork = null;
        if ($client->billing_type != 'invoiced') {
            $unpaidWork = WorkOrder::where('job_id', $job_id)->where('type', $work_type)->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit'])->whereHas('invoices', function($q) {
                return $q->where('payed_at', null);
            })->orderBy('created_at', 'desc')->first();
        }
        if ($tempWork && !$unpaidWork) {
            $data = [
                'work_id' => $tempWork->id,
                'invoice_id' => null,
            ];
        } else if (!$tempWork && $unpaidWork) {
            $pendingInvoice = $unpaidWork->invoicesPending[0];
            $data = [
                'work_id' => $unpaidWork->id,
                'invoice_id' => $pendingInvoice->id,
            ];
        } else if ($tempWork && $unpaidWork) {
            if ($tempWork->created_at > $unpaidWork->created_at) {
                $data = [
                    'work_id' => $tempWork->id,
                    'invoice_id' => null,
                ];
            } else {
                $pendingInvoice = $unpaidWork->invoicesPending[0];
                $data = [
                    'work_id' => $unpaidWork->id,
                    'invoice_id' => $pendingInvoice->id,
                ];
            }
        }
        return response()->json($data);
    }

    public function provideInfo($user_id, $id) {
        $work = WorkOrder::findOrFail($id);
        $job =  $work->job;
        $entities = $job->client->entities->pluck('firm_name','id');
    
        $parties_type = [
            'general_contractor' => 'General Contractor',
            'sub_contractor' => "Sub Contractor",
            'sub_sub' => "Sub-Sub Contractor",
            'landowner' => "Property Owner",
            'leaseholder' => 'Lease Holder (Lessee/Tenant)',

            'architect' => 'Architect',
            'condo assoc' => 'Condo Assoc',
            'Developer' => 'Developer',
            'engineer' => 'Engineer',
            'government agency' => 'Government Agency',
            'homeowners assoc' => 'Homeowners Assoc',
            'management co' => 'Management Co',
            'surveying co' => 'Surveying Co',
            'other' => 'Other'
        ];
        $gender = [
           'none' => 'Select one..',
           'female' => 'Female',
           'male' => 'Male'
        ];
        $data = [
            'job'=> $job,
            'parties_type' =>$parties_type,
            'work_order' => $work,
            'entities' => $entities,
            'gender' => $gender,
            'user_id' => $user_id,
            'attachment_types' => AttachmentType::where('slug','!=','generated')->get()->pluck('name','slug')
        ];
    
        return view('client.wizard2.provide_info',$data);
    }

    public function upload_addattachments(Request $request, $user_id, $job_id) {
        $user = User::findOrFail($user_id);
        $job = Job::findOrFail($job_id);
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
        $attachment->user_id = $user->id;
        if($request->attach_to =="job") {
            $xentity = Job::findOrFail($request->to_id);
            $xpath = 'attachments/jobs/' . $request->to_id . '/';
        } else {
            $xentity = WorkOrder::findOrFail($request->to_id);
            $xpath = 'attachments/workorders/' . $request->to_id . '/';
        }
        
        $xentity->attachments()->save($attachment);
        $attachment->save();
         
        $xfilename = "attachment-" .$attachment->id . "." . $f->guessExtension();
        
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
        
        $adminEmail = \App\AdminEmails::where('class', 'NewAttachment')->first();
        $adminUserIds = explode(',', $adminEmail->users);
        if (count($adminUserIds) > 0 && $adminEmail->users) {
            $admin_users = User::where('status',1)->whereIn('id', $adminUserIds)->get();
        } else {
            $admin_users = User::where('status',1)->isRole(['admin','researcher'])->get();
        } 
        $data = [
                'note' => 'Have been added to a Job',
                'entered_at' => $attachment->created_at->format('Y-m-d H:i:s')
        ];
        Notification::send($admin_users, new NewAttachment($attachment->id,$data,'',$user->full_name,'job'));
        
        Session::flash('message', 'Attachment was uploaded successfully.');
        return redirect()->back();
    }
}
