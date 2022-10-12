<?php

namespace App\Jobs;

use App\Job;
use App\Note;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobStatusToClosed implements ShouldQueue
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
        $aYearAgo = date('Y-m-d H:i:s', strtotime('-365 days'));
        $jobs = Job::where('created_at', '<', $aYearAgo)->where('updated_at', '<', $aYearAgo)->where(function ($q) {
            $q->where('status', '!=', 'closed')->orwhereNull('status');
        })->get();
        foreach ($jobs as $job) {
            if (! $job->client->allow_jobclose) {
                continue;
            }
            // workorders
            $works = $job->workorders()->where('updated_at', '>', $aYearAgo)->get();
            if (count($works) > 0) {
                continue;
            }
            // notes and attatchment
            $attachments = $job->attachments()->where('updated_at', '>', $aYearAgo)->get();
            if (count($attachments) > 0) {
                continue;
            }
            $notes = $job->notes()->where('updated_at', '>', $aYearAgo)->get();
            if (count($notes) > 0) {
                continue;
            }

            $job->status = 'closed';
            $note = new Note();
            $now = Carbon::now();
            $note->note_text = 'Job closed automatically due to no activity for over 1 year.';
            $note->entered_at = $now->toDateTimeString();
            $note->entered_by = 1;
            $note->viewable = 1;
            $note->noteable_type = 'App\Job';
            $note->client_id = $job->client->id;
            $note = $job->notes()->save($note);

            $job->save();
        }
    }
}
