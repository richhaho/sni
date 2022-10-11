
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

        font-size: 11pt !important;

    }
    
    .small {
    font-size: 8.5pt !important;
  
    }
    .bold {
        font-size: 11.5pt !important;
    font-weight: bold;
    }
}
</style>

@append
<div id="page">
    <div class="content">
        <h3 class="text-left">PREPARED BY/RETURN TO: <br>{{ $client_name }}</h3>
    <p>{{$client_company_name}}<br>
    {!!$client_address!!}<br>
    {{$client_phone}}</p>
    <h2 class="text-center">PARTIAL SATISFACTION OF LIEN</h2>
    <p>&nbsp;</p>
    <p>That the undersigned, as authorized agent of  {{ $client_name }},  recorded 
        a Claim of Lien (the “Claim of Lien”) against the following real property 
        owned by {{ $land_owner_name }} ; and situated in {{ $job_county }} 
        COUNTY, Florida on {{  date_format(date_create($lien_date ),'F jS, Y')}}, in Official Records  {{$field_book_page_number}} 
         in the office of the Recorder of {{ $job_county }} COUNTY, Florida
    </p>
    <table class="bold" style="width: 100%">
        <tr>
            <td style="width:50%; vertical-align: top;">
                {{$job_name}}<br>
                {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br />", $job_address)  !!}
            </td>
            <td style="width:50%; vertical-align: top;">
                {!! nl2br($legal_description) !!}<br>
                {{ $nto_number }}<br>
                {{ $job_county }} COUNTY
            </td>
        </tr>
    </table>
  
    <p>That the undersigned, does here by acknowledge this Partial Satisfaction 
        of Lien in the amount of {{ number_format($pp_amount,2) }}, as partial
        payment leaving the Claim of Lien undisturbed in the remaining amount of 
        {{ number_format($pp_outstanding,2) }}. The undersigned has the right 
        and authority to execute this Partial Satisfaction 
        of Lien and directs the County Recorder to discharge only that portion of 
        the Claim of Lien.
    </p>
    <p>
       IN WITNESS WHEREOF, the undersigned has executed this Partial Satisfaction of Lien this _____ day of {{ strtoupper($month) }}, {{ $year }}. 
    </p>
    <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <!-- @if(strlen($signature)> 0) 
                <div class="row" >
                    <div class="col-12"><img src="{{$signature}}" style="width:100%; margin-bottom: -30px"></div>
                </div>  
                @else
                    <div class="row" >
                      <div class="col-12" style="height: 60px;"></div>
                    <div class="col-12 text-center esignature" style="width:100%; height: 20px;">{{ $client_name }}</div>
                  </div> 
                @endif -->
                
                By:_______________________________________<br>
                {{$client_company_name}}<br>
                {{$client_name}}, {{$client_title}}
            </td>
        </tr>
    </table>

    <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
    <p>I HEREBY CERTIFY that on this day, before me, an officer duly authorized 
        in the State aforesaid and in the County aforesaid to take acknowledgements, 
        the foregoing instrument was acknowledged before me by means of ____ physical presence or ____ online notarization by {{$client_name}},
        as {{$client_title}} of {{$client_company_name}}, freely and voluntarily under 
        authority duly vested in him/her by said corporation. {{ $client_heshe }} is personally 
        known to me or who has produced ______________ as identification.
    </p>
    <p>
        WITNESS my hand and official seal in the County and State last aforesaid this ____ day of ______________________, {{ $year }}.
    </p>
   <div class="row">
        <div class="col-6">
            
            </div>
        <div class="col-6">

            <div class="row">
                <div class="col-12 signature" style='border-top:  1px solid black'>
                    <span class="bold">Signature of Notary Public - State of Florida</span><br>
                    <span class="small">Print,Type or Stamp Commissioned Name of Notary Public</span>
                    <span class="small">My Commission Expires:</span>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>