@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 16pt !important;
        
    }
    
    .small {
    font-size: 11pt !important;
  
    }
    .bold {
        font-size:16pt !important;
    font-weight: bold;
    }
    
     .warning {
        font-size: 16pt!important;
    }
}

@media screen {
    #page {

        font-size: 13.5pt !important;

    }
    
    .small {
    font-size: 9pt !important;
  
    }
    .bold {
        font-size: 15pt !important;
    font-weight: bold;
    }
    .warning {
        font-size: 13.5pt;
    }
}
</style>

@append

<div id="page">
    <div class="content">
<p>&nbsp;</p>
   <h1 class="text-center">NOTICE OF NONPAYMENT</h1>
  
   <p>&nbsp;</p>
   <p>Date: {{ $dated_on }}
   </p>

    <p>CERTIFIED MAIL – RETURN RECEIPT REQUESTED NO:
    @if($barcode <> '') 
        {{ $barcode }}
    @endif
    </p>
<p>&nbsp;</p>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="vertical-align: top;font-weight: bold;">To Contractor</td>
                <td>{{ $gc_firm_name }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;font-weight: bold;">Address :</td>
                <td>{!! $gc_address !!}</td>
            </tr>
            @if(isset($sureties))
            @foreach($sureties as $surety)
                <tr>
                    <td style="vertical-align: top;font-weight: bold;">To Surety</td>
                    <td>{{ $surety['name'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;font-weight: bold;">Address :</td>
                    <td>{!! $surety['address'] !!}</td>
                </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    <p>&nbsp;</p>
  
    <p class="text-justify">The undersigned notifies you that he or she has furnished {!! $materials !!} 
        for the improvement of real property identified as:</p>
        <table style="width: 100%">
            <tr>
                <td style="width: 35%; vertical-align: top;" class="warning">
                   Re:
                </td>
                <td class="warning">
                    {{ $job_name }}<br>
                    {!! $job_address !!}<br>
                    {{ $bond_number }}
                </td>
            </tr>
        </table>     
    <p>&nbsp;</p>
    <p>
        If the lienor was not in privity with the Contractor then the Notice To 
        Contractor was sent on {{ (strlen($notice_date)) ? Carbon\Carbon::parse($notice_date)->format('F jS, Y') : ''}} by Certified Mail.<br>
    <span class="warning">The amount now due and unpaid is $ {{ number_format($unpaid_balance,2)}}.</span>
    </p>

    
     <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                     <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 10%; vertical-align: top;">CC:</td>
                <td>
                      <table style="width: 100%">

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
                            </tr>
                        </tbody>
                    </table> 
                </td>
                <td>
                      @if(strlen($signature)> 0) 
                        <div class="row" >
                            <div class="col-12"><img src="{{$signature}}" style="width:250px; margin-bottom: -30px"></div>
                        </div>  
                           @else
                        <div class="row" >
                          <div class="col-12" style="height: 60px;"></div>
                        <div class="col-12 text-center esignature" style="width:100%; height: 20px;">{{ $client_name }}</div>
                      </div> 
                    @endif
                    <div class="signature" style='border-top:  1px solid black'>
                        {{$client_company_name}}
                    </div>
                    {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
                    @if(strlen($client_phone)){{ $client_phone }}<br />@endif
                     {{ $client_email }}<br />
                </td>
            </tr>
        </tbody>
     </table>

    
    
   
   

    </div>
</div>