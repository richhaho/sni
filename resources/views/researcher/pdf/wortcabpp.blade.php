
@section('css')


<style type="text/css">
@media print {
#page {
    font-size: 14pt !important;

}
.small {
    font-size: 11pt !important;
 
}
.bold {
     font-size: 14pt !important;
    font-weight: bold;
}
}


@media screen {
    #page {

        font-size: 12pt !important;

    }
    
    .small {
    font-size: 8.5pt !important;
  
    }
    .bold {
        font-size: 12pt !important;
    font-weight: bold;
    }
}
</style>

@append
<div id="page">
    <div class="content">
        <p>&nbsp;</p>
    <p>&nbsp;</p>
   <h2 class="text-center">WAIVER OF RIGHT TO CLAIM AGAINST THE PAYMENT BOND</h2>
   <h3 class="text-center">(PROGRESS PAYMENT)</h3>
   <p>&nbsp;</p>
    <p>&nbsp;</p>   
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned, in consideration of the sum of $ {{ number_format($amount,2) }},
        hereby waives its right to claim against the payment bond for labor, services, 
        or materials furnished through {{ $waiver_date }},to {{ $customer_name }} 
        on the job of {{  $land_owner_firm_name }}, for improvements to the 
        following described project:</p>
    <p>&nbsp;</p>
     <p class="text-center">
        {{ $nto_number }}, 
        {{ $job_name }}, 
        {!!  preg_replace('/\<br(\s*)?\/?\>/i', " ",$job_address) !!}, 
        {{ $job_county }}
    </p>
    <p>&nbsp;</p>
    <p class="text-justify">This waiver does not cover any retention or any labor, services, or 
        materials furnished after the date specified.</p>
    <p>Dated on: {{ $dated_on }}</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>Signed, sealed, and delivered this {{ (strlen($signed_at)) ? Carbon\Carbon::parse($signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}}</p>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%"></td>
            <td style="text-align: left">
                <div class="esignature" style="width:100%; height: 50px"></div>
                <div>
                By:_______________________________________<br>
                {{$client_company_name}}<br>
                {{$client_name}}, {{$client_title}}
                </div>
            </td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">Sworn to and subscribed before me this {{ (strlen($sworn_at)) ? Carbon\Carbon::parse($sworn_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}}
        by {{$client_name}}, who is personally known to me or produced as identification, and did take an oath.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <div style="width:20%; border-bottom: 1px solid black" class="text-center">&nbsp;</div>
    <p>Notary Public</p>
    <p>My Commission Expires:</p>
   
    </div>
</div>