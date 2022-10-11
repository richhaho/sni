@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 18pt !important;
        
    }
    
    .small {
    font-size: 9pt !important;
  
    }
    .bold {
        font-size: 18pt !important;
    font-weight: bold;
    }
}

@media screen {
    #page {

        font-size: 14pt !important;

    }
    
    .small {
    font-size: 7pt !important;
  
    }
    .bold {
        font-size: 14pt !important;
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
   <h2 class="text-center">NOTICE OF CONTEST OF LIEN</h2>
   <p>&nbsp;</p>
   <p>To: {{ $lienor_company_name }}<br/>
       {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $lienor_address) !!}
   </p>
    <p>&nbsp;</p>
    <p class="text-justify">You are notified that the undersigned contests the claim of lien filed by
         you on {{ (strlen($lien_date)) ? Carbon\Carbon::parse($lien_date)->format('F jS, Y') : ''}},
          and recorded in {{ $official_record_book }} in 
         the Public Records of {{ $job_county }} COUNTY, Florida, and that the 
         time within which you may file suit to enforce your lien is limited to 
         60 days from the date of service of this notice.  
         The lien of any lienor upon whom such notice is served and who fails to 
         institute a suit to enforce his or her lien within 60 days after service 
         of such notice shall be extinguished automatically. The clerk shall serve, 
         in accordance with s. 713.18, a copy of the notice of contest to the lien 
         claimant at the address shown in the claim of lien or most recent amendment 
         thereto and shall certify to such service and the date of service on the 
         face of the notice and record the notice</p>
    <p>&nbsp;</p>
    <p> EXECUTED on  {{ $dated_on }}.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table style="width: 100%;">
        <tr>
            <td style="width:50%"></td>
            <td style="width: 50%">
                <p>Signed:</p>
                <p>
                    {{  $land_owner_firm_name }}<br>
                    {!! $land_owner_address !!}<br>
                    @if(strlen($land_owner_email) > 0 )
                    Email: {{ $land_owner_email }}<br>
                    @endif
                    @if(strlen($land_owner_phone) > 0 )
                    Phone: {{ $land_owner_phone }}
                    @endif
            
                </p>
            </td>
        </tr>
    </table>
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    <p class="text-justify "> 
        
    </p>

    </div>
</div>