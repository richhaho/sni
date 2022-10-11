@component('mail::message')
# Monthly Payment Made
<br>
Your monthly payment (for {{$type}}) has been processed successfully.

@component('mail::panel')
Amount Charged: ${{number_format($payment->amount,2)}}<br>
Payment Type: Monthly payment for {{ $type }}<br>
<br>
 
@if ($payment->type=="credit_card")
Credit Card Name: {{$client->payeezy_cardholder_name}} <br>
Credit Card Type: {{$client->payeezy_type}} <br>
Credit Card Number: XXXX XXXX XXXX {{substr($client->payeezy_value,-4)}} <br>
Expiration Date: 20{{substr($client->payeezy_exp_date,2,2)}}-{{substr($client->payeezy_exp_date,0,2)}} <br>
@endif
Transaction Date: {{substr($payment->created_at,0,10)}}  
<br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
