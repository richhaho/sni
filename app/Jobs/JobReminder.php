<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Client;
use App\JobReminders;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendReminder;

class JobReminder implements ShouldQueue
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
        $now=date('Y-m-d 00:00:00',strtotime(\Carbon\Carbon::now()));
        $reminders=JobReminders::where('deleted_at',null)->where('status', 'scheduled')->where('date', $now)->get();
        foreach($reminders as $reminder) {
            $emails = explode(',', $reminder->emails);
            for ($i=0;$i<count($emails);$i++){
                if (!email_validate($emails[$i])){
                    unset($emails[$i]);
                }
            }
            $note = $reminder->note. '  '. url(route('client.jobs.edit',$reminder->job_id));
            $job = $reminder->job();
            if (count($emails)>0){
                Mail::to($emails)->send(new SendReminder($job->client,$note,'Job Reminder'));
            }
            $reminder->sent_at=\Carbon\Carbon::now();
            $reminder->status='sent';
            $reminder->save();
        }
    }
}

