@section('css')


<style type="text/css">
 @media print {
    #page {

        font-size: 15pt !important;

    }
    
    .small {
    font-size: 11pt !important;
  
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
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <h1 class="text-center">CONTRACTOR'S FINAL PAYMENT AFFIDAVIT</h1>
        <p>&nbsp;</p>
        <h3 class="text-left">STATE OF FLORIDA</h3>
        <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
        <p>&nbsp;</p>
        <p class="text-justify">Before me, the undersigned authority, personally appeared {{ $client_name }}, 
            who, after being first duly sworn, deposes and says of @if($client_heshe =="He") his @else her @endif personal 
            knowledge the following:
        </p>
         <ol type="1">
             <li>
                 {{$client_heshe}} is the {{ $client_title }} of {{ $client_company_name }}, which does business in the State of Florida, hereinafter referred to as the "Contractor."
             </li>
             <li>
                 Contractor, pursuant to a contract with {{ $land_owner_firm_name }} , hereinafter 
                 referred to as the “Owner,” has caused to be furnished labor, 
                 materials, and services for the construction of certain improvements 
                 to real property as more particularly set forth in said contract.
             </li>
             <li>
                 This affidavit is executed by the Contractor in accordance with 
                 section 713.06 of the Florida Statutes for the purposes of obtaining 
                 final payment from the Owner in the amount 
                 of $ {{ number_format($unpaid_balance,2) }}
             </li>
             <li>
                 All work to be performed under the contract has been fully 
                 completed, and all lienors under the direct contract have been paid 
                 in full, except the following listed lienors:
             </li>
         </ol>
        <table style="width:80%">
            <thead>
            <tr>
            <th style="width:50%">NAME OF LIENOR</th>
            <th style="width:50%">AMOUNT DUE</th>
            </tr>
            </thead>
            <tbody class="lienors">
                @foreach($lienors as $l)
                <tr>
                    <td>{{ $l['name'] }}</td>
                    <td>${{ is_numeric($l['amount']) ? number_format($l['amount'],2) : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
         <p>&nbsp;</p>
        <p> Signed, sealed, and delivered this {{ (strlen($signed_at)) ? Carbon\Carbon::parse($signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : ''}} </p>
      
           <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <div class="esignature" style="width:100%; height: 100px"></div>
                <div>
                By:_______________________________________<br>
                {{$client_company_name}}<br>
                {{$client_name}}, {{$client_title}}
                </div>
            </td>
        </tr>
    </table>
        <p>&nbsp;</p>
        <p class="text-justify">Sworn to and subscribed before me this {{ Carbon\Carbon::parse($signed_at)->format('jS \d\a\y\ \o\f\ F\, Y')}} by means of ____ physical presence or ____ online notarization by 
           {{$client_name}}, who is personally known to me or produced ________________ as 
           identification, and did take an oath.</p>
        <p>&nbsp;</p>
        <div class="col-xs-6">
            _________________________________________<br>
            Notary Public<br />
            My Commission Expires:
        </div>
    
    </div>
</div>