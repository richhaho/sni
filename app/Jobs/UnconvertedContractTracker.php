<?php

namespace App\Jobs;

use App\ContractTracker;
use App\Notifications\ContractTrackerNotConverted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class UnconvertedContractTracker implements ShouldQueue
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
        $nowDay = date('Y-m-d');
        $trackers = ContractTracker::where('is_converted', 0)->get();
        foreach ($trackers as $tracker) {
            $client = $tracker->client;
            if (! $client->has_contract_tracker) {
                continue;
            }
            $createdDay = date('Y-m-d', strtotime($tracker->created_at));
            $startdayAfter35 = date('Y-m-d', strtotime($tracker->start_date) + 35 * 24 * 3600);
            if ($nowDay > $startdayAfter35) {
                continue;
            }
            for ($over = 10; $over < 10000; $over += 10) {
                $overday = date('Y-m-d', strtotime("-$over days"));
                if ($overday < $createdDay) {
                    break;
                }
                if ($overday > $createdDay) {
                    continue;
                }

                foreach ($client->activeusers as $user) {
                    Notification::send($user, new ContractTrackerNotConverted($tracker, $over));
                }
            }
        }
    }
}
