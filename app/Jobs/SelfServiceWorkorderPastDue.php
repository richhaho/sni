<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\WorkOrder;
use Illuminate\Support\Facades\Mail;
use App\Mail\PastDueSelfWorkorder;

class SelfServiceWorkorderPastDue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    
    public function handle()
    {
        // Mail::raw('Self Service Workorder Past Due job started.', function($message)
        // {
        //     $message->subject('SelfServiceWorkorderPastDue Start');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });
        ////////////////////////////////////////////////////////////////////////////
        $after3days = date('Y-m-d H:i:s', strtotime('+3days'));
        $now = date('Y-m-d H:i:s');
        $works = WorkOrder::where('due_at', '<=', $after3days)->where('due_at', '>', $now)->where('deleted_at',null)->where('service', 'self')->whereNotIn('status',['completed','cancelled','cancelled charge','cancelled no charge','closed','cancelled duplicate','cancelled duplicate needs credit', 'temporary', 'print'])->get();
        foreach($works as $work) {
            $client = $work->job->client;
            $users = array();
            if (isset($client->users)){ $users=$client->users->where('deleted_at',null);}
            $useremails=array();
            if (count($users)>0){
                $useremails=$users->pluck('email')->toArray();
            }else{
                if($client->email) $useremails[]=$client->email;
            }
            
            for ($i=0;$i<count($useremails);$i++){
                if (!email_validate($useremails[$i])){
                    unset($useremails[$i]);
                }
            }
            Mail::to($useremails)->send(new PastDueSelfWorkorder($work));
        }
        ////////////////////////////////////////////////////////////////////////////
        // Mail::raw('Self Service Workorder Past Due job completed.', function($message)
        // {
        //     $message->subject('SelfServiceWorkorderPastDue End');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });
    }

}
