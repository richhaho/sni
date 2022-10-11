<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Client;
use App\CompanySetting;
use App\Payment;
use App\Custom\Payeezy;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMade;
use App\InvoiceBatches;
use PDF;
use Storage;

class AutopayWeekly implements ShouldQueue
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
        Mail::raw('Autopay weekly job started.', function($message)
        {
            $message->subject('Autopay Start');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });

        $clients = Client::where('deleted_at',null)->get();
        foreach($clients as $client) {
            if ($client->autopay_weekly!=1) continue;
            $invoices =  $client->invoices()->whereIn('status',['open','unpaid'])->where('batch_id',null)->orderBy('created_at')->get();
            if (!isset($invoices)) continue;
            if (count($invoices)==0) continue;
            $invoices_id=$invoices->pluck('id')->toArray();

            $client_name = $client->full_name;
            $client_companyNameCity=$client->company_name.' , '.$client->city;
 
            $total_charge= $invoices->sum('total_amount');
            $company =  CompanySetting::first();

            try {
                $company =  CompanySetting::first();
                $py = new Payeezy();
                $py->setApiKey($company->apikey);
                $py->setApiSecret($company->apisecret);
                $py->setMerchantToken($company->merchant_token);
                $py->setUrl('https://' . $company->url . '/v1/transactions');
                if ($client->company_name=="" || $client->company_name==null){
                    $client_name=$client->first_name." ".$client->last_name;
                }else{
                    $client_name=$client->company_name;
                }
            
                $payload = [
                    'merchant_ref' =>  $client_name,
                    'transaction_type'=> 'purchase',
                    'method'=> 'token',
                    'amount'=> number_format($total_charge,2,'',''),
                    'currency_code'=> 'USD',
                    'token'=> [
                        'token_type'=> 'FDToken',
                        'token_data'=> [
                            'type' =>  $client->payeezy_type,
                            'value' => $client->payeezy_value,
                            'cardholder_name' => $client->payeezy_cardholder_name,
                            'exp_date' => $client->payeezy_exp_date
                        ]
                    ]
                ];

                $result = $py->purchase($payload);
                $result_data = json_decode($result);

            if ( $result_data->transaction_status == "approved") {
                $payment = new Payment();
                $payment->invoices_id = serialize($invoices_id);
                $payment->type = 'credit_card';
                $payment->amount = $total_charge;
                $payment->client_id = $client->id;
                $payment->reference = $result_data->correlation_id;
                $payment->gateway = 'payeezy';
                $payment->transaction_status = 'approved';
                $payment->log_result = '';
                $users = $client->activeusers;
                $payment->user_id = count($users) > 0 ? $users[0]->id : 1;
                $payment->save();

                foreach($invoices as $invoice) {
                    $invoice->status ="paid";
                    $invoice->payed_at = \Carbon\Carbon::now();
                    $invoice->payment_id =$payment->id;
                    $invoice->save();
                    $work = $invoice->work_order;
                    if ($invoice->type == 'additional-service') {
                        foreach ($invoice->todos() as $todo) {
                            $todo->status = 'paid';
                            $todo->save();
                        }
                        if (count($work->incompleteTodos())) {
                            $work->has_todo = 1;
                            $work->save();
                        }
                    }
                    if( $client->billing_type != "invoiced") {
                        if ($work->status=='payment pending'){
                            $work->status = 'open';
                            $work->save();
                        }
                    }
                }

                $users = $client->activeusers;
                $mailto = [];
                foreach ($users as $user) {
                    $mailto [] = $user->email;
                }

                if (count($mailto) > 0) {
                    Mail::to($mailto)->send(new PaymentMade($total_charge, $invoices,$client,$payment->created_at));
                }
            }

            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        Mail::raw('Autopay weekly job completed.', function($message)
        {
            $message->subject('Autopay End');
            $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
            $message->to('jwatson@ironrocksoftware.com');
        });
    }
}
 