@section('css')
<style>
    @media print {
        .number {
            font-size: 18px!important;
        }
        #page {
            font-size: 11pt!important;
        }
        .content-back {
            min-height:6.9in!important;
        }
        .bold{
            font-size: 12pt!important;
            font-weight: bold;
        }
    }
    
</style>
@append


<div id="page">
    <div class="content-back">
        <div class="disclaimer">
            <table style="width: 100%">
                <tr>
                    <td style="width: 30%;vertical-align: top; ">
                        <div class="mailing-address" style="font-size: 18px; padding-right: 15px;">
                        {!! isset($client_mailing_address) ? $client_mailing_address : '&nbsp;' !!}
                        
                        </div>
                    </td>
                    <td class="text-center" style="width: 40%;vertical-align: top">
                        @if($barcode <> '') 
                            <div class="barcode"> 
                               <div class="text-center">
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG(chr(241) . $barcode, "C128",8,320) }}" alt="barcode"/>
                               </div>
                               <div class="number text-center">
                                   {{ implode( "&nbsp;",str_split($barcode , 4)) }}
                               </div>
                           </div>
                         ''
                         @else
                            @if(isset($mailing_number))
                                <div class="barcode">
                                    <div class="number text-center">
                                    @if($mailing_type=='registered-mail')
                                    REGISTERED MAIL
                                    @endif
                                    @if($mailing_type=='express-mail')
                                    EXPRESS MAIL
                                    @endif
                                    </div>
                                    <div class="number text-center">
                                    {{ $mailing_number }}
                                    </div>
                                </div>
                            @endif  
                        @endif
                    </td>
                    <td style="width: 35%;"></td>
                </tr>
            </table>
            <p><br><br><span class="bold">RETURN SERVICE REQUESTED</span></p>
            <table style="width: 100%; height: 6cm">
                <tr>
                    <td style="width: 30%"></td>
                    <td style="width: 50%; vertical-align: bottom">
                        <div class="mailing-address">
                        {!! $mailing_address !!}
                        </div>
                    </td>
                </tr>
                
            </table>
        </div>
        <div>
            &nbsp;            
            @if (isset($wo_number))
            <h3>W{{$wo_number}} @if (isset($source)) {{$source}} @endif</h3>
            @endif
            <h4><b>
            Do Not Open Returned Mail (Go to SunshineNotices.com)
            </b></h4>
            <br>
            @if (isset($envelope_wording))
            {{$envelope_wording}}
            @endif
        </div>
         
        
    </div>
</div>