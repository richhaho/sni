<?php

namespace App\Jobs;

use App\Mail\SendSubscribedReport;
use App\ReportSubscribed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscribedReport implements ShouldQueue
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
        $nowTime = date('h:i A');
        $day = date('l');
        $subscribedReports = ReportSubscribed::where('weekdays', 'like', "%$day%")->where('time', $nowTime)->get();
        foreach ($subscribedReports as $subscribe) {
            $emails = explode(',', $subscribe->users);
            for ($i = 0; $i < count($emails); $i++) {
                if (! email_validate($emails[$i])) {
                    unset($emails[$i]);
                }
            }
            $report = $subscribe->report();
            $client_id = $subscribe->client_id;

            if (count($emails) > 0) {
                Mail::to($emails)->send(new SendSubscribedReport($report, $client_id));
            }
        }
    }
}
