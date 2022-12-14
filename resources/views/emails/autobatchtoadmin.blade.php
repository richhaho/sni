@component('mail::message')
# Invoice Batches Generated by AutoBatch

Dear Administrator:

 
Below are invoice batches generated at period {{$period}}.
<br><br>
 
<table style="padding: 0px !important;border-spacing:0px !important">
	<thead>
		<tr>
			<th width="20%" style="text-align: center;border: 1px solid gray">ID</th>
			<th width="40%" style="text-align: center;border: 1px solid gray">Client</th>
			<th width="20%" style="text-align: center;border: 1px solid gray">Count of Invoices</th>
			<th width="20%" style="text-align: center;border: 1px solid gray">Total Amount</th>
		</tr>
	</thead>
	<tbody>
		@foreach($batches as $batch)
		<tr>
			<td style="text-align: center;border: 1px solid gray">{{$batch->id}}</td>
			<td style="text-align: center;border: 1px solid gray">{{\App\Client::where('id',$batch->client_id)->first()->company_name}}</td>
			<td style="text-align: center;border: 1px solid gray">{{count(unserialize($batch->invoice_id))}}</td>
			<td style="text-align: center;border: 1px solid gray">${{$batch->total_amount}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<br><br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent