@component('mail::message')
# Notice

{{$recipient->firm_name}}<br>
@if(strlen($recipient->attention_name))
{{$recipient->attention_name}}  
@endif
{!! nl2br($recipient->address) !!}
<br><br>
@if($recipient->work_order->type=="amended-notice-to-owner" || $recipient->work_order->type=="notice-to-owner")
YOU ARE IN RECEIPT OF A NOTICE TO OWNER THAT HAS BEEN PREPARED ON BEHALF OF OUR CLIENT.

<p class="text-justify warning">THIS NOTICE IS NOT A LIEN OR A CLOUD ON THE TITLE TO THE PROPERTY DESCRIBED 
AND IS NOT A MATTER OF PUBLIC RECORD.  IT DOES NOT CONSTITUTE A DOUBT ABOUT 
THE CREDIT WORTHINESS OF THE PERSONS OR COMPANY NAMED HEREIN.  IT IS 
SIMPLY A SOUND BUSINESS PRACTICE AND A DEVICE WHICH PROTECTS ALL PARTIES CONCERNED.  
FLORIDA STATUTES 713 AND 255.05 REQUIRE THAT <span class="warning">{{ $client_company_name->company_name }}</span> AS A SUPPLIER
OF MATERIALS AND/OR SERVICES, PROVIDE YOU, AS THE OWNER, WITH A NOTICE NOT LATER THAN FORTY-FIVE (45) 
DAYS FROM THE DATE WE FIRST PROVIDE LABOR, SERVICES, OR MATERIALS TO YOU. 
<br>
</p>
@else
YOU ARE IN RECEIPT OF @if($recipient->work_order->type=='out-of-state-nto-preliminary-notice-of-lien-rights') AN @else A @endif {{strtoupper(str_replace('-',' ',$recipient->work_order->type))}} THAT HAS BEEN PREPARED ON BEHALF OF {{ $client_company_name->company_name }}.<br><br>
@endif
@if($recipient->work_order->type!='claim-of-lien' && substr($recipient->work_order->type, 0, 20) !='notice-of-nonpayment' && $recipient->work_order->type != 'notice-of-non-payment')
IF YOU WOULD LIKE TO USE THIS SERVICE, PLEASE VISIT <a href="WWW.SUNSHINENOTICES.COM">WWW.SUNSHINENOTICES.COM</a> FOR MORE INFORMATION ON THIS AS WELL AS OTHER SERVICES.
@endif
<br><br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent