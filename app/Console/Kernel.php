<?php



namespace App\Console;



use Illuminate\Console\Scheduling\Schedule;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

Use App\Jobs\WeeklyOutstanding;
Use App\Jobs\JobinfoEstated;
Use App\Jobs\EmailSMSReminder;
Use App\Jobs\AutoBatch;
Use App\Jobs\AutopayWeekly;
Use App\Jobs\JobStatusToClosed;
Use App\Jobs\ClearResearch;
Use App\Jobs\SelfServiceWorkorderPastDue;
use App\Jobs\AutoRenewSubscription;
use App\Jobs\JobReminder;
use App\Jobs\SubscribedReport;
use App\Jobs\JobCloseWithRelease;
use App\Jobs\RemoveOldNotifications;
use App\Jobs\JobLastDayOver;
use App\Jobs\MonthlyPayMornitoringUser;
use App\Jobs\MonthlyRecurringPay;
use App\Jobs\UnconvertedContractTracker;

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

