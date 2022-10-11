@component('mail::message')
# Work Order Completed

Your notice number {{ $work_order->number }} has been completed.

@if(count($invoices) > 0 )
These are the unpaid invoices for this notice:

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
@else

@endif 



Thanks,<br>
{{ config('app.name') }}
@endcomponent