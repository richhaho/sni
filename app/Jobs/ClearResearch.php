<?php

namespace App\Jobs;

use App\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearResearch implements ShouldQueue
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
        $aHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $jobs = Job::where('research_start', '<', $aHourAgo)->where('research_start', '!=', null)->where('research_complete', null)->get();
        foreach ($jobs as $job) {
            $job->research_start = null;
            $job->save();
            // workorders
            if (count($job->workorders) == 0) {
                continue;
            }
            $work = $job->firstWorkorder();
            if ($work) {
                $work->researcher = null;
                $work->save();
            }
        }
    }
}
