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
                           
                        @endif
                    </td>
                    <td style="width: 35%;"></td>
                </tr>
            </table>
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
        </div>
        <div class="footer-back">
            &nbsp;
        </div>
    </div>
</div>