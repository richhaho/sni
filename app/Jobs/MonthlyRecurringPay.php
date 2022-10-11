<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Client;
use App\CompanySetting;
use App\SubscriptionPayment;
use App\Custom\Payeezy;
use Mail;
use App\Mail\MonthlyPaymentMade;

class MonthlyRecurringPay implements ShouldQueue
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
        // Mail::raw('MonthlyRecurringPay job started.', function($message)
        // {
        //     $message->subject('MonthlyRecurringPay Start');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });
        ////////////////////////////////////////////////////////////////////////
        $nowMonth = date('y-m');
        $clients = Client::where('deleted_at', null)->where('has_contract_tracker', 1)->where('montly_recurring_price', '>', 0)->get();
        foreach($clients as $client) {
            if (strlen($client->payeezy_type)==0) {
                continue;
            }
            $payeezy_exp = substr($client->payeezy_exp_date, -2) . '-' . substr($client->payeezy_exp_date, 0, 2);
            if ($payeezy_exp < $nowMonth) {
                continue;
            }

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
                    'amount'=> number_format($client->montly_recurring_price,2,'',''),
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
                    $payment = new SubscriptionPayment();
                    $payment->type = 'credit_card';
                    $payment->amount = $client->montly_recurring_price;
                    $payment->client_id = $client->id;
                    $payment->reference = $result_data->correlation_id;
                    $payment->gateway = 'payeezy';
                    $payment->transaction_status = $result_data->transaction_status;
                    $payment->log_result = $result;
                    $payment->service_type = 'monthly-recurring-payment';
                    $users = $client->activeusers;
                    $payment->save();
            
                    $users = $client->activeusers;
                    foreach ($users as $user) {
                        $mailto [] = $user->email;
                    }

                    if(json_encode(unserialize($client->override_payment))!="false" && json_encode(unserialize($client->override_payment))!="null"){
                        Mail::to(unserialize($client->override_payment))->send(new MonthlyPaymentMade($payment,$client, 'recurring price'));
                    }else{
                        $mailto = array();
                        $users = $client->activeusers;
                        foreach ($users as $user) {
                            $mailto [] = $user->email;
                        }
                        if (count($mailto) > 0) {
                            Mail::to($mailto)->send(new MonthlyPaymentMade($payment,$client, 'recurring price'));
                        }
                    }
                }

            } catch (Exception $e) {
                echo $e->getMessage();
            }

        }
        ////////////////////////////////////////////////////////////////////////
        // Mail::raw('MonthlyRecurringPay job completed.', function($message)
        // {
        //     $message->subject('MonthlyRecurringPay End');
        //     $message->from('no-reply@sunshinenotice.com', 'SunshineNotice');
        //     $message->to('jwatson@ironrocksoftware.com');
        // });
    }

}
