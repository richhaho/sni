<?php

namespace App\Console;

use App\Jobs\AutoBatch;
use App\Jobs\AutopayWeekly;
use App\Jobs\AutoRenewSubscription;
use App\Jobs\ClearResearch;
use App\Jobs\EmailSMSReminder;
use App\Jobs\JobCloseWithRelease;
use App\Jobs\JobinfoEstated;
use App\Jobs\JobLastDayOver;
use App\Jobs\JobReminder;
use App\Jobs\JobStatusToClosed;
use App\Jobs\MonthlyPayMornitoringUser;
use App\Jobs\MonthlyRecurringPay;
use App\Jobs\RemoveOldNotifications;
use App\Jobs\SelfServiceWorkorderPastDue;
use App\Jobs\SubscribedReport;
use App\Jobs\UnconvertedContractTracker;
use App\Jobs\WeeklyOutstanding;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.

     *

     * @var array
     */
    protected $commands = [

        Commands\SendNotification::class,

    ];

    /**
     * Define the application's command schedule.

     *

     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule

     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new JobinfoEstated())->everyFiveMinutes();

        $schedule->job(new WeeklyOutstanding())->weekly()->mondays()->at('06:00');

        $schedule->job(new EmailSMSREminder())->everyMinute();

        $schedule->job(new AutoBatch())->weekly()->fridays()->at('17:00');

        $schedule->job(new AutopayWeekly())->weekly()->fridays()->at('18:00');

        $schedule->job(new JobStatusToClosed())->daily();

        $schedule->job(new ClearResearch())->everyFiveMinutes();

        $schedule->job(new SelfServiceWorkorderPastDue())->daily();

        $schedule->job(new AutoRenewSubscription())->hourly();

        $schedule->job(new JobReminder())->daily()->at('06:00');

        $schedule->job(new SubscribedReport())->everyMinute();

        $schedule->job(new JobCloseWithRelease())->daily();

        $schedule->job(new RemoveOldNotifications())->daily();

        $schedule->job(new JobLastDayOver())->daily();

        $schedule->job(new MonthlyPayMornitoringUser())->monthly();

        $schedule->job(new MonthlyRecurringPay())->monthly();

        $schedule->job(new UnconvertedContractTracker())->daily();

        // $schedule->job(new MonthlyPayMornitoringUser())->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.

     *

     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
