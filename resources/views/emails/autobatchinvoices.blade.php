@component('mail::message')
# AutoBatch Invoices

Dear {{ $client_name}}:

 
Below are your batched invoices.
<br>
To pay these invoices login to our portal <a href="{{ route("client.invoicesbatches.index") }}">here</a> and select invoice batches and pay.

@component('mail::panel')
Batch Number: #{{$invoicebatch->id}}<br>
Batch Date: {{date('m/d/Y',strtotime($invoicebatch->created_at))}}<br>
Total Amount: ${{$invoicebatch->total_amount}}<br>
@endcomponent
<br>
Please review attached pdf to see more detail for batch.

Thanks,<br>
{{ config('app.name') }}
@endcomponent