@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 15pt !important;

    }
    
    .small {
    font-size: 9pt !important;
  
    }
    .bold {
        font-size: 15pt !important;
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
            <h3 class="text-left">PREPARED BY/RETURN TO:<br>
            {{ $client_name }}</h3>
    <p>{{$client_company_name}}<br>
    {!!$client_address!!}<br>
    {{$client_phone}}</p>
    <h2 class="text-center">NOTICE OF BOND</h2>
    <p>&nbsp;</p>
    <div class="row">
        <div class="col-xs-1">To:</div>
        <div class="col-xs-11">
            {{ $lienor_name }}<br>
            {!!$lienor_address!!}
        </div>
    </div>
   
    <p class="text-justify">
        You are hereby notified that the Claim of Lien filed by you on {{ (strlen($lien_date)) ? Carbon\Carbon::parse($lien_date)->format('F jS, Y') : ''}}, 
        and recorded in Official Records {{ $field_book_page_number }} 
        of the Public Records of {{ $job_county }} COUNTY FLORIDA, Is secured by a bond, a copy being attached.
    </p>
   
   <p>&nbsp;</p>
   <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>Dated this ____ day of {{ title_case($month) }}, {{ $year }}</p>
    <p>&nbsp;</p>

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
    <p>&nbsp;</p>
    <p class="text-justify">
        The undersigned certifies that a copy of this Notice of Bond and a 
        copy of the Bond have been served upon {{ $lienor_name }} at the address 
        specified above on the ____ day of {{ title_case($month) }}, {{ $year }}, 
        in accordance with Section 713.23, Florida Statutes.
    </p>
    <p>
        SWORN TO AND SUBSCRIBED before me on this _____ day of {{ title_case($month) }}, 
        {{ $year }}, by {{ $client_name }}, {{$client_title}}, of 
        {{$client_company_name}},  who is Personally known _____ OR Produced 
        Identification_______ Type of Identification Produced________________ .
    </p>
   <div class="row">
        <div class="col-6">
            
            </div>
        <div class="col-6">
<p>&nbsp;</p>
            <div class="row">
                <div class="col-12 signature" style='border-top:  1px solid black'>
                    Signature of Notary Public - State of Florida<br>
                    Print,Type or Stamp Commissioned Name of Notary Public<br>
                    My Commisison Expires:
                </div>
            </div>
        </div>
    </div>
    </div>
</div>