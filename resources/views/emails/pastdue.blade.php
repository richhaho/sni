@component('mail::message')
# Self-Service Work Order due soon.
Your notice number {{ $work_order->number }} will come due within 3 days. To see your notice, login to our portal <a href="{{ route("client.notices.edit", $work_order->id) }}">here</a> and process your work order.
<br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent