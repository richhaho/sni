<?php

namespace App\Jobs;

use App\Client;
use App\Mail\OutstandingInvoices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WeeklyOutstanding implements ShouldQueue
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
        Mail::raw('Weekly Outstanding Invoice email job started.', function ($message) {
            $message->subject('WeeklyOutstanding Start');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });

        $clients = Client::where('deleted_at', null)->get();
        foreach ($clients as $client) {
            if ($client->autobatch == 1) {
                continue;
            }
            $client_name = $client->full_name;
            //$invoices =  $client->open_invoices;
            $invoices = $client->invoices->whereIn('status', ['open', 'unpaid']);
            $client_companyNameCity = $client->company_name.' , '.$client->city;

            if (isset($invoices)) {
                if (count($invoices) > 0) {
                    $owed_amount = $invoices->sum('total_amount');
                    if (isset($client->users)) {
                        $users = $client->users->where('deleted_at', null);
                    }
                    $useremails = [];
                    if (count($users) > 0) {
                        $useremails = $users->pluck('email')->toArray();
                    } else {
                        if ($client->email) {
                            $useremails[] = $client->email;
                        }
                    }

                    $useremails[] = 'Suzanne@sunshinenotices.com';

                    for ($i = 0; $i < count($useremails); $i++) {
                        if (! email_validate($useremails[$i])) {
                            unset($useremails[$i]);
                        }
                    }

                    if (json_encode(unserialize($client->override_weekly)) != 'false' && json_encode(unserialize($client->override_weekly)) != 'null') {
                        Mail::to(unserialize($client->override_weekly))
                    ->cc(['Suzanne@sunshinenotices.com'])
                    ->send(new OutstandingInvoices($owed_amount, $invoices, $client_name, $client_companyNameCity));
                    } else {
                        if (count($useremails) > 1) {
                            Mail::to($useremails)
                        ->cc(['Suzanne@sunshinenotices.com'])
                        ->send(new OutstandingInvoices($owed_amount, $invoices, $client_name, $client_companyNameCity));
                        }
                    }
                }
            }
        }

        Mail::raw('Weekly Outstanding Invoice email job completed.', function ($message) {
            $message->subject('WeeklyOutstanding End');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });
    }
}
  function email_validate($email)
  {
      if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return false;
      }

      return true;
  }
