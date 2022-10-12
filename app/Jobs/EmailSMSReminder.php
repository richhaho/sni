<?php

namespace App\Jobs;

use App\Client;
use App\Mail\SendReminder;
use App\Reminders;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailSMSReminder implements ShouldQueue
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
        // Mail::raw('SMS Reminder email job started.', function($message)
        // {
        //     $message->subject('Email SMS Reminder Start');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });

        $now = date('Y-m-d H:i', strtotime(\Carbon\Carbon::now()));
        $period = ['Daily' => 'day', 'Weekly' => 'week', 'Monthly' => 'month'];

        $accountId = env('TWILLO_ACCOUNT_ID');
        $token = env('TWILLO_TOKEN');
        $fromNumber = env('TWILLO_PHONE');

        $twilio = new \Aloha\Twilio\Twilio($accountId, $token, $fromNumber);

        $reminderss = Reminders::where('deleted_at', null)->where('status', 1)->get();
        $clients = Client::where('deleted_at', null)->get();
        foreach ($reminderss as $reminder) {
            $reminder_date = date('Y-m-d H:i', strtotime($reminder->next_send_date));
            if ($reminder_date != $now) {
                continue;
            }
            $reminder->end_send_date = $now;
            $reminder->next_send_date = date('Y-m-d H:i:00', strtotime('+'.$reminder->send_frequency.' '.$period[$reminder->period]));
            $reminder->save();

            foreach ($clients as $client) {
                /////////////Email Reminder to client//////////////
                if ($client->allow_email_reminder != 0) {
                    $useremails = [];
                    $users = $client->users->where('deleted_at', null);
                    if (count($users) > 0) {
                        $useremails = $users->pluck('email')->toArray();
                    } else {
                        if ($client->email) {
                            $useremails[] = $client->email;
                        }
                    }
                    for ($i = 0; $i < count($useremails); $i++) {
                        if (! email_validate($useremails[$i])) {
                            unset($useremails[$i]);
                        }
                    }

                    if ($reminder->email_message && $reminder->email_subject) {
                        if (json_encode(unserialize($client->override_email_reminder)) != 'false' && json_encode(unserialize($client->override_email_reminder)) != 'null') {
                            Mail::to(unserialize($client->override_email_reminder))->send(new SendReminder($client, $reminder->email_message, $reminder->email_subject));
                        } else {
                            if (count($useremails) > 0) {
                                Mail::to($useremails)->send(new SendReminder($client, $reminder->email_message, $reminder->email_subject));
                            }
                        }
                    }
                }
                /////////////SMS Reminder to client//////////////
                if ($client->allow_sms_reminder != 0) {
                    if ($reminder->sms_message) {
                        if (json_encode(unserialize($client->override_sms_reminder)) != 'false' && json_encode(unserialize($client->override_sms_reminder)) != 'null') {
                            $phones = unserialize($client->override_sms_reminder);
                            foreach ($phones as $phone) {
                                try {
                                    $twilio->message($phone, $reminder->sms_message);
                                } catch (\Exception $e) {
                                }
                            }
                        }
                    }
                }
            }
        }

        // Mail::raw('SMS Reminder email job completed.', function($message)
        // {
        //     $message->subject('Email SMS Reminder End');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });
    }
}
