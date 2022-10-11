<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Job;
use App\Note;
use Carbon\Carbon;

class JobCloseWithRelease implements ShouldQueue

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
        $day7Ago = date('Y-m-d H:i:s', strtotime("-7 days"));
        $jobs=Job::where(function($q) {
          $q->where('status','!=','closed')->orwhereNull('status');
        })->get();
        foreach($jobs as $job) {
          if (!$job->client->allow_jobclose) continue;
          // notes and attatchment 
          $attachments = $job->attachments()->where('type', 'release')->where('is_final_release', 1)->where('autoclosed_job', '!=', 1)->where('updated_at','<', $day7Ago)->get();
          if (count($attachments)==0) continue;
          foreach($attachments as $attachment) {
            $attachment->autoclosed_job = 1;
            $attachment->save();
          }

          $job->status = 'closed';
          $note = New Note();
          $now = Carbon::now();
          $note->note_text = 'Job Closed 7 Days From Release of Lien.';
          $note->entered_at = $now->toDateTimeString();
          $note->entered_by = 1;
          $note->viewable = 1;
          $note->noteable_type = 'App\Job';
          $note->client_id=$job->client->id;
          $note = $job->notes()->save($note);

          $job->save();
        }
    }
}

