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
    <p>&nbsp;</p>
   <h2 class="text-center">NOTICE OF NON PAYMENT WITH INTENT TO LIEN AND FORECLOSE</h2>
   <p>&nbsp;</p>
   <p>Date: {{ date_format(date_create($dated_on ),'F jS, Y') }}
   </p>
  
   

    <p>&nbsp;</p>
    <table style="width: 100%">
            <tr>
                    <td style="font-weight: bold;width 25%">To Owner:</td>
                    <td>{{ $land_owner_firm_name }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;font-weight: bold;width 25%">Address :</td>
                    <td>{!! $land_owner_address !!}</td>
                </tr>
            <tr>
                <td style="width: 25%; vertical-align: top;" class="warning">
                   Re:
                </td>
                <td class="warning">
                    {{ $nto_number }}<br>
                    {{ $job_name }}<br>
                    {!! $job_address !!}<br>
                    
                </td>
            </tr>
        </table>    
    </table>
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned notifies you that he or she has furnished {!! $materials !!} 
        for the improvement of real property identified as {{$nto_number}}, {{ $job_name }}, {{ preg_replace('/\<br(\s*)?\/?\>/i', " ", $job_address) }}
        , under an order given by {{ $customer_name }}. <br><span class="bold">The amount now due and unpaid is $ {{ number_format($unpaid_balance,2)}}</span></p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
     <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                        <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td style="width: 10%;vertical-align: top;">CC:</td>
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
                    Very truly yours,
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                      @if(strlen($signature)> 0) 
                    <div class="row" >
                        <div class="col-12"><img src="{{$signature}}" style="width:250px; margin-bottom: -30px"></div>
                    </div>  
                        @else
                        <div class="row" >
                          <div class="col-12" style="height: 60px;"></div>
                        <div class="col-12 text-cente esignature" style="width:100%; height: 20px;font-style: italic;">{{ $client_name }}</div>
                      </div> 
                    @endif
                    <div class="signature" style='border-top:  1px solid black'>
                        {{$client_company_name}}
                    </div>
                    {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
                    {{ $client_name }}<br />
                    {{ $client_phone }}<br />
                    
                </td>
            </tr>
        </tbody> 
     </table>
    <br>
     <p class="small">Copies sent by Certified Mail - Return Receipt and/or Regular Mail
    </p>
    
    
    
     

    </div>
</div>