@component('mail::message')
# {{$subject}}
<br>
{{$message}}
<br>
@component('mail::button',['url'=>route('client.reminderEmail.notAllow',$client->id)])
Unsubscribe
@endcomponent
<br>         
Thanks,<br>
{{ config('app.name') }}
@endcomponent