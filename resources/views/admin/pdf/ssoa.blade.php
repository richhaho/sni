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
.noprint{
    display: none;
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
     
     
  
    <p>{{ $land_owner_firm_name }}</p>
    <p>{!! $land_owner_address !!}</p>
    <p>Via Email: {{ $land_owner_email }}</p>
    
   <h1 class="text-center">Sworn Statement of Account</h1>
   <p>&nbsp;</p>
  
    <table style="width: 100%">
        <tr>
            <td style="font-weight: bold;width:15%">Job Name:</td>
            <td>{{ $job_name }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:15%"></td>
            <td>{!! $job_address !!}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:15%"></td>
            <td>{{ $job_county }} COUNTY</td>
        </tr>
    </table>&nbsp;
    <p class="text-justify">
        In accordance with Section 713.16(5), Florida Statutes, and in response to your
        demand for a sworn statement in your letter dated {{(strlen($demand_date)) ? Carbon\Carbon::parse($demand_date)->format('F jS, Y') : '_____________' }}, the
        undersigned submits the following statement:</p>
 
    <table style="width: 100%">
        <tr>
            <td style="font-weight: bold;width:40%">1. Services performed:</td>
            <td>{{$services_performed}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">2. Total amount of the contract:</td>
            <td>${{number_format($total_contract_amount,2)}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">3. Total amount of Change Orders:</td>
            <td>${{number_format($total_changeorder_amount,2)}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">4. Net amount of contract:</td>
            <td>${{number_format($total_contract_amount+$total_changeorder_amount,2)}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">5. Total amount invoiced:</td>
            <td>${{number_format($total_invoice_amount,2)}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">6. Total paid to date:</td>
            <td>${{number_format($total_paid_date,2)}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;width:40%">7. Amount due:</td>
            <td>${{number_format(abs($total_invoice_amount-$total_paid_date),2)}}</td>
        </tr>
    </table>&nbsp;
    <p>
        {{$construction_text}}
    </p>
    <p>Sincerely</p>
    <p>Signed, sealed, and delivered this {{ (strlen($sworn_signed_at)) ? Carbon\Carbon::parse($sworn_signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : '_____________'}}</p>
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
     
    <p class="text-justify">Sworn to and subscribed before me this {{ (strlen($sworn_signed_at)) ? Carbon\Carbon::parse($sworn_signed_at)->format('jS \d\a\y\ \o\f\ F\, Y') : '____________'}} by means of ____ physical presence or ____ online notarization
        by {{$client_name}} as {{$client_title}} of {{$client_company_name}} who is personally known to me or produced as identification,  and did take an oath.</p>
    <br> 
    <table style="width: 100%">
        <tr>
             
            <td style="width: 30%">
                <div style="width:30%; border-bottom: 1px solid black;" class="text-center small"><br></div>
                <p> Notary  &nbsp;&nbsp;Public</p>
                <p> My Commission Expires:</p>
                <div style="width:30%; border-bottom: 1px solid black" class="text-center small"><br></div>
            </td>
        </tr>
    </table>    
     
    </div>
</div>