@section('css')


<style type="text/css">
 @media print {
    #page {

        font-size: 13pt !important;

    }
    
    .small {
    font-size: 10pt !important;
  
    }
    .bold {
        font-size: 13pt !important;
    font-weight: bold;
    }
}

@media screen {
    #page {

        font-size: 10pt !important;

    }
    
    .small {
    font-size: 7pt !important;
  
    }
    .bold {
        font-size: 11pt !important;
    font-weight: bold;
    }
}
</style>

@append

<div id="page">
    <div class="content">
        <h3 class="text-left">PREPARED BY/RETURN TO:<br>
            {{ $client_name }}</h3>
    <p>{{$client_company_name}}<br>
    {!!$client_address!!}<br>
    {{$client_phone}}</p>
    <h3 class="text-center">WARNING!</h3>
    <p class="warning text-justify">THIS LEGAL DOCUMENT REFLECTS THAT A CONSTRUCTION LIEN HAS BEEN PLACED ON THE REAL PROPERTY LISTED HEREIN. UNLESS THE OWNER OF SUCH PROPERTY TAKES ACTION TO SHORTEN THE TIME PERIOD, THIS LIEN MAY REMAIN VALID FOR ONE YEAR FROM THE DATE OF RECORDING, AND SHALL EXPIRE AND BECOME NULL AND VOID THEREAFTER UNLESS LEGAL PROCEEDINGS HAVE BEEN COMMENCED TO FORECLOSE OR TO DISCHARGE THIS LIEN.</p>
    <h3 class="text-center">AMENDED CLAIM OF LIEN</h3>
    <p class="warning text-center">{{$amend_reason}}<br>
        Originally Recorded in Official Records {{$original_or_book}} on {{ $original_date}}
    </p>
    
    <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
    <p class="text-justify">BEFORE ME, the undersigned authority, personally appeared {{ $client_name }}, who, 
        being duly sworn deposes and says that {{ $client_name }} is the {{$client_title}} 
        of the lienor herein, {{$client_company_name}} whose address is  {{ preg_replace('/\<br(\s*)?\/?\>/i', " ", $client_address) }}, and that in 
        accordance with a contract with {{ $customer_name }}. Lienor furnished:
        {!!$materials!!} on the following described real property in {{ $job_county }} COUNTY, Florida:
        
    </p>
    <table class="bold" style="width: 100%">
        <tr>
            <td style="width:50%">
                {{ $nto_number }}<br>
                {{$job_name}}<br>
                {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br />", $job_address) !!}
            </td>
            <td style="width:50%">
                @if(isset($noc))
                NOC: {{ $noc }}<br>
                @endif
                {!! nl2br($legal_description) !!}<br>
                {{ $job_county }} COUNTY FL
            </td>
        </tr>
    </table>
    <p class="text-justify" style="margin-top: 10px;">
       Owned by  {{ $land_owner_name }}@if(strlen($leaseholders[0]['full_name'])>0) and Leased by {{ $leaseholders[0]['full_name'] }} @endif, 
       of a total value of $ {{number_format($job_contract_amount,2)}} of which there remains an unpaid balance 
       of $ {{number_format($unpaid_balance,2)}}  @if ($interest_amount > 0) plus interest in the amount of $ {{number_format($interest_amount,2)}} @endif
        @if(strlen($job_start_date)>0)
       . Furnishing the first of the items on {{ date_format(date_create($job_start_date),'F jS, Y')}} and 
       @else
        .
       @endif
       @if(strlen($job_last_date)>0)
        the last of the items on {{ date_format(date_create($job_last_date),'F jS, Y')}}
       @else
        THE WORK IS IS CURRENTLY ONGOING
       @endif
       @if(strlen($nto_date))
       ; and <strong>(if the lien is claimed by one not in privity with the owner)</strong> that the Lienor served Notice to Owner to all parties involved on {{ date_format(date_create($nto_date),'F jS, Y') }} by Certified and/or Regular Mail.
       @else
       .
       @endif
    </p>

    <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <div class="esignature" style="width:100%; height: 50px"></div>
                <div>
                By:_______________________________________<br>
                {{$client_company_name}}<br>
                {{$client_name}}, {{$client_title}}
                </div>
            </td>
        </tr>
    </table>

    <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
    <p class="text-justify">I HEREBY CERTIFY that on this day, before me, an officer duly authorized 
        in the State aforesaid and in the County aforesaid to take acknowledgements, 
        the foregoing instrument was acknowledged before me by {{$client_name}},
        as {{$client_title}} of {{$client_company_name}}, freely and voluntarily under 
        authority duly vested in him/her by said corporation. {{ $client_heshe }} is personally 
        known to me or who has produced ______________ as identification.
    </p>
    <p>
        WITNESS my hand and official seal in the County and State last aforesaid this ____ day of ______________________, ________.
    </p>
   <div class="row">
       <table style="width: 100%">
           <tr>
               <td style="width: 50%">
                   <span>Copies furnished to the following by Certified Mail, Return Receipt Requested and Regular Mail:</span>
                <table style="width: 100%">
                        <thead>
                            <tr>
                               <!-- <th style="text-align: left;width: 30%">TYPE</th>-->
                                <th style="text-align: left;">NAME</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($parties))       
                            @foreach($parties as $gc)
                            <tr>
                                
                                <td>{{ $gc['company_name']}}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
            
               </td>
               <td style="width:50%">
                    <div class="row">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                    <div class="col-12 signature" style='border-top:  1px solid black'>
                        <span class="bold">Signature of Notary Public - State of Florida</span><br>
                        <span class="small">Print,Type or Stamp Commissioned Name of Notary Public</span>
                    </div>
                    </div>
               </td>
           </tr>
       </table>
        
    </div>
 
    </div>
</div>