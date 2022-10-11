@component('mail::message')
# Outstanding Invoices

Dear {{ $client_name}}:

 
Below are your outstanding invoices.
<br>
To pay these invoices login to our portal <a href="{{ route("client.invoices.index") }}">here</a> and select invoices and pay.


@component('mail::panel')
Amount Owed: ${{number_format($owed_amount,2)}}
@endcomponent



@foreach($invoices as $invoice) 
@component('mail::panel')
    Invoice ID: {{$invoice->id}} 
<br>Job Number: {{ $invoice->work_order->job->number}}
<br>Job Name: {{ $invoice->work_order->job->name}}
<?php 
$work=$invoice->work_order;
$customer_name='';
$customer=$work->job->parties->where('type','customer')->first();

if (count($customer)>0){
    $customer_contact= $customer->contact;
    if(count($customer_contact)>0){
        //$name=$customer_contact->first_name.' '.$customer_contact->last_name;
        $customer_name=$customer->firm->firm_name;
    };
}
?>
<br>Customer Name: {{$customer_name}}
<br>Work Order Type: {{ $invoice->work_order->order_type->name}}
 

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