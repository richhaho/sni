@section('css')
<style>
    @media print {
        .number {
            font-size: 18px!important;
        }
        #page {
            font-size: 9pt!important;
        }
        .content-back {
            min-height:6.0in!important;
        }
        .text-justify{
            font-size: 8pt!important;
        
        }
        .lastparagraph{
            font-size: 11pt!important;
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
                    <td style="width: 30%;vertical-align: top">
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

        <div >
            &nbsp;
            @if (isset($wo_number))
            <p>W{{$wo_number}} @if (isset($source)) {{$source}} @endif</p>
            @endif
            <p><b>
            Do Not Open Returned Mail (Go to SunshineNotices.com)
            </b></p>
            <br>
            @if (isset($envelope_wording))
            <p>{{$envelope_wording}}</p>
            @endif   
        </div>
        </div>
        <div class="footer-back" style="margin-top: 1.5cm">
        <p class="text-justify warning">THIS NOTICE IS NOT A LIEN OR A CLOUD ON THE TITLE TO THE PROPERTY DESCRIBED 
            AND IS NOT A MATTER OF PUBLIC RECORD.  IT DOES NOT CONSTITUTE A DOUBT ABOUT 
            THE CREDIT WORTHINESS OF THE PERSONS OR COMPANY NAMED HEREIN.  IT IS 
            SIMPLY A SOUND BUSINESS PRACTICE AND A DEVICE WHICH PROTECTS ALL PARTIES CONCERNED.  
            FLORIDA STATUTES 713 AND 255.05 REQUIRE THAT <span class="warning">{{ $client_company_name }}</span> AS A SUPPLIER
            OF MATERIALS AND/OR SERVICES, PROVIDE YOU, AS THE OWNER, WITH A NOTICE NOT LATER THAN FORTY-FIVE (45) 
            DAYS FROM THE DATE WE FIRST PROVIDE LABOR, SERVICES, OR MATERIALS TO YOU.</p>
        <p class="text-justify">THE ENCLOSED “NOTICE TO OWNER” IS A FORM PRESCRIBED BY 
            THE LAWS OF THE STATE OF FLORIDA.  ITS PURPOSE IS TO LET YOU KNOW THAT 
            <span class="warning">{{ $client_company_name }}</span> IS FURNISHING 
            LABOR, SERVICES, OR MATERIALS FOR IMPROVEMENT OF REAL PROPERTY AS 
            DESCRIBED IN THE ENCLOSED NOTICE.</p>
        <p class="text-justify">TO ENSURE THAT THE SUBJECT PROPERTY NAMED REMAINS FREE 
            AND CLEAR OF A LIEN OR BONDS CLAIM, THE PERSONS OR COMPANIES NAMED AS ORDERING 
            MATERIALS AND/OR SERVICES SHOULD PROVIDE YOU WITH A “PARTIAL RELEASE” AND/OR “FINAL RELEASE OF 
            LIEN” FROM <span class="warning">{{ $client_company_name }}</span>.  
            THIS WILL ENSURE THAT ALL LABOR, SERVICES, AND MATERIALS PROVIDED BY 
            <span class="warning">{{ $client_company_name }}</span> ARE PAID TO 
            US ON A TIMELY BASIS. PRIOR TO MAKING FINAL PAYMENT TO THE CONTRACTOR, 
            THE OWNER SHOULD ENSURE THAT HE HAS BEEN PROVIDED  WITH A “FINAL RELEASE 
            OF LIEN” FROM <span class="warning">{{ $client_company_name }}</span> 
            CONFIRMING THAT WE HAVE RECEIVED PAYMENT IN FULL FOR ALL LABOR, SERVICES, 
            AND MATERIALS PROVIDED BY US TO THE PROPERTY DESCRIBED ON THE ENCLOSED NOTICE.</p>
        <p class="text-justify">
            SHOULD YOU HAVE FURTHER QUESTIONS REGARDING THE “NOTICE TO OWNER” PLEASE 
            FEEL FREE TO CONTACT <span class="warning">{{ $client_company_name }}</span>, 
            <span class="warning">{{ isset($client_signature) ? $client_signature :'' }}</span> EITHER BY PHONE 
            <span class="warning">{{ isset($client_phone) ? $client_phone : '' }}</span> OR EMAIL AT 
            <span class="warning">{{ isset($client_email) ? $client_email : '' }}</span>.  THANK YOU FOR YOUR 
            COOPERATION IN THIS REGARD. WE LOOK FORWARD TO DOING BUSINESS WITH YOU IN THE FUTURE.
        </p>
        @if (isset($client_address))
        <p class="text-justify">
            <span class="warning">{{ $client_company_name }}</span> HEREBY DEMANDS A 
            VERIFIED COPY OF ANY PROVISION OF THE LEASE THAT PROHIBITS LIABILITY FOR 
            THE IMPROVEMENTS BEING MADE BY THE LESSEE (WHERE APPLICABLE).  PLEASE 
            FURNISH THE VERIFIED COPY (IN COMPLIANCE WITH §92.525 FLORIDA STATUTES 
            OF LEASE PROVISION TO THE UNDERSIGNED BY FAX TO:  <span class="warning">{{ $client_fax }}</span>, 
            BY EMAIL TO: <span class="warning">{{ $client_email }}</span> OR BY CERTIFIED 
            MAIL TO: <span class="warning">{{ $client_company_name }} {{ preg_replace('/\<br(\s*)?\/?\>/i', " ", $client_address)}}.</span>  
            IF YOU FAIL TO SERVE A VERIFIED COPY OF THE LEASE PROVISION WITHIN 30 
            DAYS OF THIS DEMAND, OR SERVE A FALSE OR FRAUDULENT COPY, YOUR INTEREST 
            AS LESSOR SHALL BE SUBJECT TO A CONSTRUCTION LIEN UNDER CHAPTER 713, PART 
            1 FLORIDA STATUTES IN FAVOR OF THE UNDERSIGNED.
        </p>
        @endif
            <table style="width: 100%;">
                <tr>
                    <td>
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                            ->size(150)
                            ->generate('https://sunshinenotices.com')) !!}">
                    </td>
                    <td>
                    Visit sunshinenotices.com<br>
                    <span class="warning lastparagraph">THIS NOTICE IS BEING PREPARED USING ONLINE 
                    SOFTWARE SUPPLIED BY SUNSHINE NOTICES INC.  IF YOU WOULD LIKE TO USE 
                    THIS SERVICE, PLEASE VISIT WWW.SUNSHINENOTICES.COM FOR MORE INFORMATION 
                    ON THIS AS WELL AS OTHER SERVICES.</span>
                    </td>
                </tr>
            </table>
        </div>

        
    </div>
</div>