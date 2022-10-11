@section('css')


<style type="text/css">
   #page  h1 {
        display: block;
        font-size: 1.5em;
        margin-top: 0px;
        margin-bottom: 0px;
        font-weight: bold;
    }
@media print {
#page {
    font-size: 14pt !important;

}
.small {
    font-size: 10pt !important;
 
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
    font-size: 9pt !important;
  
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
   <h1 class="text-center">WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT</h1>
   <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned lienor, in consideration of the Final payment in the sum 
        of $ {{ number_format($amount,2) }}, hereby waives and releases its lien and right to claim 
        a lien for labor, services, or materials furnished to {{ $customer_name }}, 
        on the job of {{  $land_owner_firm_name }} to the following property:</p>
    <p>&nbsp;</p>
    <p>
        {{ $nto_number }}<br>
        {{ $job_name }}<br>
        {!! $job_address !!}<br>
        {{ $job_county }} COUNTY<br>
    </p>
    <p>&nbsp;</p> 
    <p>Signed, sealed, and delivered this {{ (strlen($sworn_signed_at)) ? Carbon\Carbon::parse($sworn_signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}}</p>
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
                <p>
                     
                    {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
                    {{$client_email}}
                </p>
            </td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">Sworn to and subscribed before me this {{ (strlen($sworn_signed_at)) ? Carbon\Carbon::parse($sworn_signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}} by means of ____ physical presence or ____ online notarization
        by {{$client_name}}, who is personally known to me or produced _______________ as identification, and did take an oath.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <div style="width:20%; border-bottom: 1px solid black" class="text-center">&nbsp;</div>
    <p>Notary Public</p>
    <p>My Commission Expires:</p>
    </div>
</div>