@component('mail::message')
# Sunshine Notices: {{$report->name}}
<br>
We are sending report with CSV.
<br>
@component('mail::button',['url'=>route('client.folders.index')])
Reporting
@endcomponent
<br>         
Thanks,<br>
{{ config('app.name') }}
@endcomponent