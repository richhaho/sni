@component('mail::message')
# Payment Made

Your payment has been processed successfully.



@component('mail::panel')
Amount Charged: ${{number_format($payment_amount,2)}}<br>
<?php
$payment=\App\Payment::where('id',$invoices[0]->payment_id)->first();
 
if ($payment->type=="credit_card"){

?>
Credit Card Name: {{$client->payeezy_cardholder_name}} <br>
Credit Card Type: {{$client->payeezy_type}} <br>
Credit Card Number: XXXX XXXX XXXX {{substr($client->payeezy_value,-4)}} <br>
Expiration Date: 20{{substr($client->payeezy_exp_date,2,2)}}-{{substr($client->payeezy_exp_date,0,2)}} <br>
<?php
}
?>
Transaction Date: {{substr($transaction_date,0,10)}}  
@endcomponent

@if(count($invoices) > 1 )
Here is your transaction summary:
@else
Here are your transaction summary:
@endif 

@foreach($invoices as $invoice) 
@component('mail::panel')
    Invoice ID: {{$invoice->id}} Job Name: {{ $invoice->work_order->job->name}}
@endcomponent

@component('mail::table')
|Item|Price|Quantity|Totals|
|---|:---:|:---:|---:|
@foreach ($invoice->lines as $line)
|{{$line->description}}| ${{ number_format($line->price,2)}}|  {{ $line->quantity }}|  ${{ number_format($line->amount,2)}}|
@endforeach
|&nbsp;|&nbsp;|**Total**|**${{number_format($invoice->total_amount,2)}}**|
@endcomponent

@endforeach


Thanks,<br>
{{ config('app.name') }}
@endcomponent
