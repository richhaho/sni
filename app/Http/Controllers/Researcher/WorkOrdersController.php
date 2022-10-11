<?php

namespace App\Http\Controllers\Researcher;

use App\Attachment;
use App\AttachmentType;
use App\Http\Controllers\Controller;
use App\Job;
use App\WorkOrder;
use App\WorkOrderType;
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
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAttachment;
use Mail;
use App\Mail\NoticeComplete;

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
        $this->wo_types = WorkOrderType::all()->pluck('name','slug')->toArray();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       
       $clients =  Client::get()->sortby('company_name')->pluck('company_name', 'id')->prepend('All',0);
       
       $works = WorkOrder::query();
       
       
       if (session()->has('work_order_filter.client')) {
           $xclient = session('work_order_filter.client');
           $works->whereHas('job',function($q) use ($xclient) {
               return $q->where('client_id',$xclient);
           });
           $jobs_list = Job::where('client_id',$xclient)->get()->sortby('name')->pluck('name','id')->prepend('All',0);
       }  else {
           $jobs_list = Job::all()->sortby('name')->pluck('name','id')->prepend('All',0);
            
       }
       
       if (session()->has('work_order_filter.job')) {
           $xjob = session('work_order_filter.job');
           if ($jobs_list->contains(function($value,$key) use ($xjob){ return $key == $xjob; })) {
               $works->where('job_id',session('work_order_filter.job'));
           } else {
               session()->forget('work_order_filter.job');
           }
           
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
           if(session('work_order_filter.work_status') != 'all') {
                $works->where('status',session('work_order_filter.work_status'));
                
           } else {
             
           }
       }
       
        if (session()->has('work_order_filter.work_condition')) {
           switch (session('work_order_filter.work_condition')) {
               case 1:
                    $works->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge', 'closed', 'cancelled duplicate','cancelled duplicate needs credit']);
                    break;
               case 2:
                   $works->whereIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit']);
                    break;
               default:
           }
       } else {
           session(['work_order_filter.work_condition' => 1]);
            $works->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit']);
       }
       
       if (session()->has('work_order_filter.daterange')) {
            if(session('work_order_filter.daterange') != '') {
                $dates = explode(' - ',session('work_order_filter.daterange'));
                
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);

                //$works->whereBetween('due_at',[$from_date,$to_date])->orderBy('due_at');
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $works->where([['due_at','>=',$from],['due_at','<=',$to]])->orderBy('due_at','desc');
            }
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
       
       $works= $works->orderBy('id','DESC')->paginate(15);
       
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
        
       $conditions = [
           '0' => 'All',
           '1' => 'Open',
           '2' => 'Close',
       ];
       Session::put('backUrl',\URL::full());
       $data = [
           'clients' => $clients,
           'works' => $works,
           'wo_types' => ['all' => 'All'] + $this->wo_types,
           'statuses' => ['all' => 'All'] + $this->statuses,
           'conditions' => $conditions,
           'jobs' => $jobs_list,
           'available_notices' => $available_notices
       ];
         
       return view('researcher.workorders.index',$data);
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
        $jobs_list = Job::where('status','!=','closed');
        $jobs_list = $jobs_list->pluck('name','id')->toArray();
      
       
       
        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'job_id' => $job_id,
            
        ];
        
        return view('researcher.workorders.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
         $this->validate($request, [
            'due_at' => 'required|date|date_format:m/d/Y',
            'job_id' => 'required|exists:jobs,id',
            'type' => 'required',
            
        ]); 
        $data = $request->all();
        $job = Job::findOrFail($request->job_id);
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
        $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));
        if($job->client->billing_type == "invoiced") {
            $data['status'] = 'open';
        } else {
             $data['status'] = 'pending';
        }
        
        if ($request->has('last_day')) {
            if(strlen(trim($data['last_day']))>0) {
                
                $job->last_day = date('Y-m-d', strtotime($data['last_day']));
                $job->save();
            }
        }
        $wo =  WorkOrder::create($data);
        
        Session::flash('message', 'Work Order ' .$wo->number . ' created');
        return redirect()->route('workorders.edit',$wo->id);
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
            return view('researcher.workorders.show',$data);
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
        if (session()->has('note')) {
            $xnote = session('note');
        } else {
            $xnote = "";
        }
        $work = WorkOrder::findOrFail($id);
        //dd($work->invoicesPending);
        $jobs_list = Job::all()->pluck('name','id')->toArray();
        $attachment_types= AttachmentType::get()->pluck('name','slug');
        $data = [
            'jobs_list' => $jobs_list,
            'wo_types' => $this->wo_types,
            'statuses' => $this->statuses,
            'work' => $work,
            'attachment_types' => $attachment_types,
            'parties_type' => $this->parties_type,
            'xnote' => $xnote,
            'available_notices' =>$available_notices
        ];
        
        return view('researcher.workorders.edit',$data);
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
        $this->validate($request, [
            'due_at' => 'required|date|date_format:m/d/Y',
            'job_id' => 'required|exists:jobs,id',
            'type' => 'required',
            'status' => 'required',
        ]); 
        $wo =  WorkOrder::findOrFail($id);
        $data = $request->all();
        
        if ($request->has('is_rush')) {
            $data['is_rush'] = 1;
        } else {
            $data['is_rush'] = 0;
        }
        $data['due_at'] = date('Y-m-d', strtotime($data['due_at']));
         $data['mailing_at'] = date('Y-m-d', strtotime($data['mailing_at']));
        
         if($wo->status <> 'completed') {
             if($request->status == 'completed') {
                  $mailto = array();
                 $users = $wo->job->client->users;
                 foreach ($users as $user) {
                        $mailto [] = $user->email;
                  }
                 if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new NoticeComplete($wo->id,$wo->invoicesPending));
                 }
                 
             }
         }
         
        $wo->update($data);
        
        Session::flash('message', 'Work Order ' .$wo->number . ' updated');
        return redirect()->route('workorders.edit',$wo->id);
        
        
        
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
        $wo_id=$work->id;
        $invoice=Invoice::where('work_order_id',$wo_id)->get();
        $len= count($invoice);
        
        // echo json_encode($invoice);
        // return;

        if ($len>0){
            Session::flash('message','This work order has an invoice. Please delete the invoice first before deleting this work order.');
        }else{
            $temp_name = $work->number;
            $work->delete();

            // redirect
            Session::flash('message', 'Work Order  ' .$temp_name . ' successfully deleted.');
        }
        return redirect()->route('workorders.index');
        
    }
    
    
     public function uploadattachment($id,Request $request) {
        $work = WorkOrder::findOrFail($id);
        $client = $work->job->client;
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
        
        
        if ($request->has('notify')) {
            $data = [
                'note' => 'Have been added to a Notice',
                'entered_at' =>  $attachment->created_at->format('Y-m-d H:i:s')
            ];
            Notification::send($client->users, new NewAttachment($attachment->id,$data,Auth::user()->full_name,'notice'));
        }
        Session::flash('message', 'Attachment added');
        return redirect()->route('workorders.edit',['id'=>$id,'#attachments']);
    }
    
    
    public function showattachment($workorder_id,$id) {
      
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
        $work_id = $attachment->attachable_id;
        if (is_null($attachment->thumb_path)) {
            
        } else {
            Storage::delete($attachment->thumb_path);
        }
        Storage::delete($attachment->file_path);
        $attachment->delete();
        
        Session::flash('message', 'Attachment removed');
        return redirect()->route('workorders.edit',['id'=>$work_id,'#attachments']);
    }
    
    public function setfilter (Request $request) {
        if ($request->has('resetfilter')) {
               if($request->resetfilter=='true') {
                   session()->forget('work_order_filter');
               }
        }
        
        
        if ($request->has('client_filter')) {
            if($request->client_filter == 0 ) {
                session()->forget('work_order_filter.client');
            } else {
                session(['work_order_filter.client' => $request->client_filter]);
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

         if ($request->has('work_number')) {
            if($request->work_number == '' ) {
                session()->forget('work_order_filter.work_number');
            } else {
                session(['work_order_filter.work_number' => $request->work_number]);
            }
         }
        return redirect()->route('workorders.index');
    }
    
    
    public function resetfilter (Request $request) {
         session()->forget('work_order_filter');
        return redirect()->route('workorders.index');
    }
    public function createinvoice (Request $request,$work_id) {
        $work = WorkOrder::findOrFail($work_id);
        $client =  $work->job->client;
       
        $template = Template::where('type_slug',$work->type)->where('client_id',$client->id)->first();
        $doit = false;
        if($template) {
            $doit = true;
        } else {
            $template = Template::where('type_slug',$work->type)->where('client_id',0)->first();
            if($template) {
                $doit = true;
            }
        }
        
        $invoice = new Invoice();
        $invoice->client_id  = $client->id;
        $invoice->work_order_id  = $work->id;
        switch ($client->billing_type) {
            case 'none':
            case 'attime' :
                $invoice->due_at = \Carbon\Carbon::now();
                break;
            case 'invoiced':
                $invoice->due_at =  new \Carbon\Carbon('next friday');  
                break;
        }
        $invoice->status = "open";
        $invoice->total_amount = 0;
        $invoice->save();
        $total_amount = 0;
        
        if($doit) {

            
            foreach($template->lines as $tline) {
                
                if ($tline->type =="apply-rush" && $work->is_rush) {
                    $line = New InvoiceLine();
                    $line->invoice_id = $invoice->id;
                    $line->description = $tline->description;
                    $line->quantity = $tline->quantity;
                    $line->price = $tline->price;
                    $line->amount = $tline->quantity * $tline->price;
                    $line->status = "";
                    $total_amount += $line->amount;
                    $line->save();
                } else {
                    
                }
                if ($tline->type == "apply-always") {
                   
                    $line = New InvoiceLine();
                    $line->invoice_id = $invoice->id;
                    $line->description = $tline->description;
                    $line->quantity = $tline->quantity;
                    $line->price = $tline->price;
                    $line->status = "";
                    $line->amount = $tline->quantity * $tline->price;
                    $total_amount += $line->amount;
                    $line->save();
                }
                
            }
            $invoice->total_amount = $total_amount;
            $invoice->save();
            
        } else {
             
        }
        if ($request->has('from')) {
            return redirect()->to(route('invoices.edit', $invoice->id) ."?from=workorder");
         } else {
            return redirect()->route('invoices.edit', $invoice->id);
         }
    }
    public function checkType($job_id,$type) {
        $work_orders = WorkOrder::where('job_id',$job_id)->where('type',$type)->get();
        if (count($work_orders)> 0 ) {
            return "YES";
        } 
        return "NO";
    }
}
