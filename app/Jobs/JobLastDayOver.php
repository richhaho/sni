<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\JobLastdayOver30_45_60_75;
use App\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendToPartiesOnNtoAndAnto;

class JobLastDayOver implements ShouldQueue
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
        $overdays = ['30', '45', '60', '75'];
        foreach ($overdays as $over) {
          $overday = date('Y-m-d', strtotime("-$over days"));
          $jobs=Job::where('deleted_at', null)->where('last_day', 'like', "$overday%")->where(function($q) {
            $q->where('status','!=','closed')->orwhereNull('status');
          })->get();
          foreach($jobs as $job) {
            $client = $job->client;
            if (!$client->turn_job_reminder) continue;

            $works = $job->workorders()->whereIn('type', ['notice-to-owner', 'amended-notice-to-owner'])->get();
            if (count($works)==0) continue;
            $works = $job->workorders()->whereIn('type', ['notice-of-non-payment', 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713', 'notice-of-nonpayment-for-government-jobs-statutes-255', 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose', 'claim-of-lien'])->get();
            if (count($works)>0) continue;

            $useremails=array();
            $useremails[]='Suzanne@sunshinenotices.com';
            foreach($job->parties as $party) {
              $contact = $party->contact;
              if (!isset($contact->email)) continue;
              $useremails[]=$contact->email;
            }
            for ($i=0;$i<count($useremails);$i++){
              if (!email_validate($useremails[$i])){
                  unset($useremails[$i]);
              }
            }

            if(json_encode(unserialize($client->override_lastday_over))!="false" && json_encode(unserialize($client->override_lastday_over))!="null"){
              Mail::to(unserialize($client->override_lastday_over))
                  ->cc(['Suzanne@sunshinenotices.com'])
                  ->send(new SendToPartiesOnNtoAndAnto($job, $over));
            }else{
                if (count($useremails)>1){
                    Mail::to($useremails)
                      ->cc(['Suzanne@sunshinenotices.com'])
                      ->send(new SendToPartiesOnNtoAndAnto($job, $over));
                }
            }

            foreach($client->activeusers as $user) {
              Notification::send($user, new JobLastdayOver30_45_60_75($job, $over));
            }
          }
        }
    }
}

