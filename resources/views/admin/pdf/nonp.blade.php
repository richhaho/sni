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
        <p class="pull-right">@if ($nto_number) JOB #:{{$nto_number}} @endif</p>
<p>&nbsp;</p>
   <h1 class="text-center">Demand For Payment</h1>
  
   <p>&nbsp;</p>
   <p>Date: {{ date_format(date_create($dated_on ),'F jS, Y') }}
   </p>

    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 30%">To :</td>
                <td>{{ $gc_firm_name }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 30%">Address :</td>
                <td>{!! $gc_address !!}</td>
            </tr>
            

            @if(isset($sureties))

            @foreach($sureties as $surety)
                
                <tr>
                    <td style="vertical-align: top;font-weight: bold;width: 30%">To Surety</td>
                    <td>{{ $surety['name'] }} </td>
                </tr>
                 
                <tr>
                    <td style="vertical-align: top;font-weight: bold;width: 30%">Address :</td>
                    <td>{!! $surety['address'] !!}</td>
                </tr>
                
            @endforeach
            @endif
        </tbody>
    </table>
    <p>&nbsp;</p>
  
    <p class="text-justify">The undersigned notifies you that @if ($client_gender=='female') she @else he @endif has furnished {!! $materials !!} under an order given by {{$customer_name}} 
        for the improvement of real property identified as:</p> <br>
        <table style="width: 100%">
            <tr>
                <td style="width: 30%; vertical-align: top;" class="warning">
                   Re:
                </td>
                <td class="warning">
                    {{ $job_name }}<br>
                    {!! $job_address !!}<br>
                     
                </td>
            </tr>
        </table>     
    <p>&nbsp;</p>
    <p>
        <span class="warning">THE AMOUNT NOW DUE AND UNPAID IS $ {{ number_format($unpaid_balance,2)}}.</span><br><br>
        If payment has not been received by {{$force_date ? $force_date:'_________'}}, then this matter will be referred for legal action.
        <br><br>
        {!! $additional_text !!}
        @if ($additional_text)
        <br><br>
        @endif
        Thank you for your attention to this matter, <br>
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
&nbsp;
    <p class="small">Copies sent by Certified Mail - Return Receipt and/or Regular Mail
    </p>
    
    
   
   

    </div>
</div>