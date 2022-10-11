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
   <h1 class="text-center">Rescission Letter</h1> <p></p><h2 class="text-center">(Cancels out all or part of NTO)</h2>
   <p>&nbsp;</p>
    
   <p style="border-bottom: 1px solid black"></p>

   <p>Date: {{ date_format(date_create($dated_on ),'F j, Y') }}
   </p>
  
    <table style="width: 100%">
            <tr>
                    <td style="font-weight: bold;width 25%">To:</td>
                    <td>{{ $land_owner_firm_name }}</td>
            </tr>
            <tr>
                    <td style="vertical-align: top;font-weight: bold;width 25%">Address :</td>
                    <td>{!! $land_owner_address !!}</td>
            </tr>
    </table>
    <p>&nbsp;</p>
    <p>Dear Sir/Madam:</p>
    <p></p>
    <p class="text-justify">
    Please disregard this notice {{$text_content}}    
    </p>
    <table style="width: 100%">
            <tr>
                <td style="width: 15%; vertical-align: top;" class="warning">
                   Job Info:
                </td>
                <td >
                    <table>
                    <tr>
                        <td style="width: 50%; vertical-align: top;" >
                            Notice Number:
                        </td>
                        <td >
                            {{ $nto_number }}        
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top;" >
                            Date: 
                        </td>
                        <td >
                            {{ date_format(date_create($nto_last_date ),'F j, Y') }}
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 50%; vertical-align: top;" >
                            Property Description: 
                        </td>
                        <td >
                            {!! $job_name !!}    <br>
                            {!! $job_address !!}        
                        </td>
                    </tr>
                </table>
                    
                    
                </td>
            </tr>
    </table>
    <p></p>
    <p class="text-justify">We apologize for any inconvenience, 
    </p>

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
                    Sincerely,
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
     
    
    
    
     

    </div>
</div>