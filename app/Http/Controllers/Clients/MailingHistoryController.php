<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BatchDetail;
use App\Attachment;
use App\WorkOrder;
use App\WorkOrderType;
use App\Client;
use App\WorkOrderRecipient;
use App\Job;
use App\PdfPage;
use PDF;
use Storage;
use Auth;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use App\Template;
use App\TemplateLine;
use Mail;
use App\Mail\NoticeComplete;
use Session;

class MailingHistoryController extends Controller
{
    public function index() {
        
        $doc_ids = BatchDetail::where('client_id',Auth::user()->client->id)->pluck('attachment_id')->toArray();
        
        $available_documents = Attachment::query()->whereIn('id', $doc_ids)->where('type','generated')->orderBy('created_at','desc')->with('recipient');

       
       if (session()->has('mailinghistory_filter.mailing_type')) {
          $available_documents->whereHas('recipient',function($q) {
              $q->where('mailing_type',session('mailinghistory_filter.mailing_type'));
          });
       }
      
       if (session()->has('mailinghistory_filter.job')) {
          
            $cin = WorkOrder::whereHas('job',function($q) {
               $q->where('jobs.id',session('mailinghistory_filter.job'));
           })->pluck('id');
   
           $available_documents->whereIn('attachable_id',$cin);
           
       }
       if (session()->has('mailinghistory_filter.wo_types')) {
          
            $win = WorkOrder::where('type',session('mailinghistory_filter.wo_types'))->pluck('id');
   
           $available_documents->whereIn('attachable_id',$win);
           
       }
       
        if (session()->has('mailinghistory_filter.barcode')) {
          $available_documents->whereHas('recipient',function($q) {
              $q->where('barcode',session('mailinghistory_filter.barcode'));
          });
       }

       if (session()->has('mailinghistory_filter.daterange')) {
            if(session('mailinghistory_filter.daterange') != '') {
                $date_range=session('mailinghistory_filter.daterange');
                $date_type="/^[0-1][0-9]-[0-3][0-9]-[0-9]{4} - [0-1][0-9]-[0-3][0-9]-[0-9]{4}$/";
                
               if (preg_match($date_type,$date_range)){
                $dates = explode(' - ',session('mailinghistory_filter.daterange'));
                //dd($dates);
                $from_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[0]);
                $to_date = \Carbon\Carbon::createFromFormat('m-d-Y', $dates[1]);
                $from=substr($from_date,0,10).' 00:00:00';
                $to=substr($to_date,0,10).' 23:59:59';
                $available_documents->where([['printed_at','>=',$from],['printed_at','<=',$to]])->orderBy('printed_at','desc');
                Session::flash('message', null);
                 }else{
                    Session::flash('message', 'Input not in expected range format');
                 }
            }
        }


        //dd($available_documents);
        //$xdoc = $available_documents->first();
        //dd($xdoc->recipient->mailing_type);
      
        $mailing_types = [
            'all' => 'All',
            'standard-mail' => 'Regular Mail',
            'certified-green' => 'Certfied Green RR',   
            'certified-nongreen' => 'Certfied Non Green', 
            'registered-mail' => 'Registered Mail',
            'express-mail' => 'Express Mail',
            'other-mail' => 'eMail',
        ];
        
       $wo_types = ['0' => 'All'] + WorkOrderType::all()->sortBy('name')->pluck('name','slug')->toArray();
        $jobs= ['all' =>'All'] + Job::where('client_id',Auth::user()->client->id)->orderBy('name')->pluck('name','id')->toArray();
        $data =  [
            'mailings' => $available_documents->paginate(100),
            'mailing_types' =>$mailing_types,
             'wo_types' => $wo_types,
           'jobs' => $jobs
        ];
        
        
        return view('client.mailinghistory.index',$data);
        
    }
    
    public function resend(Request $request,$id) {
        //return $request->all();


        $attachment = Attachment::findOrFail($id);
        if($attachment->resent == -1) {
            $attachment->resent = 0;
            $attachment->resent_reason=null;
        } else {
            $attachment->resent = -1;
            if ($request['reason_note']){
              $attachment->resent_reason=$request['reason_note'];
            } else{
              $attachment->resent_reason='';
            }

        }
        $attachment->save();
        return redirect()->route('client.mailinghistory.index');
        
    }
    
    
      public function setfilter (Request $request) {
        
        if ($request->has('resetfilter')) {
               if($request->resetfilter=='true') {
                   session()->forget('mailinghistory_filter');
               }
        }
        


        if ($request->has('client_filter')) {
            if($request->client_filter == 0 ) {
                session()->forget('mailinghistory_filter.client');
            } else {
                session(['mailinghistory_filter.client' => $request->client_filter]);
            }
        }
        
        if ($request->has('mailing_type')) {
           
            if($request->mailing_type == 'all' ) {
                session()->forget('mailinghistory_filter.mailing_type');
            } else {
                session(['mailinghistory_filter.mailing_type' => $request->mailing_type]);
            }
        }
        
        if ($request->has('job')) {
           
            if($request->job == 'all' ) {
                session()->forget('mailinghistory_filter.job');
            } else {
                session(['mailinghistory_filter.job' => $request->job]);
            }
        }
        
         if ($request->has('barcode')) {
                if($request->barcode == '' ) {
                    session()->forget('mailinghistory_filter.barcode');
                } else {
                    session(['mailinghistory_filter.barcode' => $request->barcode]);
                }
         }
         
         
          if ($request->has('wo_types')) {
                if($request->wo_types == '0' ) {
                    session()->forget('mailinghistory_filter.wo_types');
                } else {
                    session(['mailinghistory_filter.wo_types' => $request->wo_types]);
                }
         }

         if ($request->has('daterange')) {
            if($request->daterange == '' ) {
                session()->forget('mailinghistory_filter.daterange');
            } else {
                session(['mailinghistory_filter.daterange' => $request->daterange]);
            }
         }

        
        return redirect()->route('client.mailinghistory.index');
    }
    
    public function resetfilter (Request $request) {
         session()->forget('mailinghistory_filter');
        return redirect()->route('client.mailinghistory.index');
    }
}
