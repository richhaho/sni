@section('css')


<style type="text/css">
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

        font-size: 11.5pt !important;

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
        <h3 class="text-left">PREPARED BY/RETURN TO:<br>
            {{ $client_name }}</h3>
    <p>{{$client_company_name}}<br>
    {!!$client_address!!}<br>
    {{$client_phone}}</p>
    <h2 class="text-center">SATISFACTION OF LIEN</h2>
    <p>&nbsp;</p>
    <p class="text-justify">That the undersigned, as authorized agent for  {{$client_company_name}}, hereby 
        releases the property hereinafter described (the “Real Property”) from 
        that certain Claim of Lien recorded by {{ $client_name }} in the office 
        of the Recorder of {{ $job_county }} COUNTY, Florida, on {{  date_format(date_create($lien_date),'F jS, Y')}}
        in Official Records {{ $field_book_page_number }}, and hereby directs the Recorder to cancel the Claim of Lien.
        @if(strlen($job_last_date)>0)
         The Real Property is owned by @if ($land_owner_name ){{$land_owner_name}} @else __________ @endif and is legally described as follows:
        @else
         However, this Satisfaction of Lien is specifically limited to amounts due and owing for materials and/or labor incorporated into the project through @if($thru_date) {{$thru_date}}. @else __/__/2018. @endif  Lienor maintains and reserves all prospective lien rights accruing from @if($thru_date) {{$thru_date}}  @else __/__/2018  @endif to completion of the project. The Real Property is owned by @if ($land_owner_name ){{$land_owner_name}} @else __________ @endif and is legally described as follows:
        @endif
    </p>
    <p>&nbsp;</p>
    <table class="bold" style="width: 100%">
         <tr>
             <td style="width:50%; vertical-align: top;">
                 {{ $nto_number }}<br>
                 {{$job_name}}<br>
                 {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br />", $job_address) !!}
             </td>
             <td style="width:50%; vertical-align: top;">
                 {!! nl2br($legal_description) !!}<br>
                 
                 {{ $job_county }} COUNTY FL
             </td>
         </tr>
     </table>
    <p>&nbsp;</p>
    <p class="text-justify">
       IN WITNESS WHEREOF, the undersigned has executed this Satisfaction of Lien this _____ day of {{ strtoupper($month) }}, {{ $year }}. 
    </p>

    <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <div class="esignature" style="width:100%; height: 100px"></div>
                By:_______________________________________<br>
                {{$client_company_name}}<br>
                {{$client_name}}, {{$client_title}}
            </td>
        </tr>
    </table>
    
    
    <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
    <p>&nbsp;</p>
    <p class="text-justify">I HEREBY CERTIFY that on this day, before me, an officer duly authorized 
        in the State aforesaid and in the County aforesaid to take acknowledgements, 
        the foregoing instrument was acknowledged before me by means of ____ physical presence or ____ online notarization by {{$client_name}},
        as {{$client_title}} of {{$client_company_name}}, freely and voluntarily under 
        authority duly vested in him/her by said corporation. He/She is personally 
        known to me or who has produced ______________ as identification.
    </p>
    <p>
        WITNESS my hand and official seal in the County and State last aforesaid this ____ day of ______________________, {{ $year }}.
    </p>
    <p>&nbsp;</p>
  
 <div class="row">
       <table style="width: 100%">
           <tr>
               <td style="width: 50%">
                   
               </td>
               <td style="width:50%">
                    <div class="row">
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