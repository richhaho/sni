
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
   <h3 class="text-center">(FINAL PAYMENT)</h3>
   <p>&nbsp;</p>
    <p>&nbsp;</p>   
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned, in consideration of the final payment in the amount of 
        $ {{ number_format($amount,2) }}, hereby waives its right to claim 
        against the payment bond for labor, services, or materials furnished to 
        {{ $customer_name }} on the job of {{  $land_owner_firm_name }}, for improvements to the following described project:</p>
    <p>&nbsp;</p>
     <p class="text-center">
        {{ $nto_number }}, 
        {{ $job_name }}, 
        {!!  preg_replace('/\<br(\s*)?\/?\>/i', " ",$job_address) !!}, 
        {{ $job_county }} COUNTY
    </p>
    <p>&nbsp;</p>
    <p>Dated on: {{ date_format(date_create($dated_on),'F jS, Y') }}</p>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    <table style="width: 100%">
        <tr>
            <td style="width: 50%"></td>
            <td style="text-align: right">
                <!-- @if(strlen($signature)> 0) 
                <div class="row" >
                    <div class="col-12"><img src="{{$signature}}" style="width:250px; margin-bottom: -30px"></div>
                </div>  
                @else
                        <div class="row" >
                          <div class="col-12" style="height: 60px;"></div>
                        <div class="col-12 text-center esignature" style="width:100%; height: 20px;">{{ $client_name }}</div>
                      </div> 
                @endif -->
                By:_________________________________________________<br>
                
            </td>

        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">Sworn to and subscribed before me this {{ (strlen($sworn_at)) ? Carbon\Carbon::parse($sworn_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}} by means of ____ physical presence or ____ online notarization
        by {{$client_name}}, who is personally known to me or produced as identification, and did take an oath.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <div style="width:20%; border-bottom: 1px solid black" class="text-center">&nbsp;</div>
    <p>Notary Public</p>
    <p>My Commission Expires:</p>
     
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p style="font-style: italic">
        (3) A person may not require a claimant to furnish a waiver that is 
        different from the forms in subsections (1) and (2).
    </p>
   <p style="font-style: italic">
        (4) A person who executes a waiver in exchange for a check may condition 
        the waiver on payment of the check.
    </p>
    <p style="font-style: italic">
        (5) A waiver that is not substantially similar to the forms in this 
        section is enforceable in accordance with its terms.
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    
    </div>
</div>