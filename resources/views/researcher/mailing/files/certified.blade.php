@extends('researcher.mailing.pdf_certified')
@section('content')
<div id="page">
    <table class="header"  >
        <tbody>
            <tr>
                <td style="width: 186px;vertical-align: top;border: 1px black solid">
                    <div class="font-size-8 ">Name and Address of Sender</div>
                    <div  style="margin-top: 0.3cm;">
                    <div class="font-size-12 text-center">{{$company_name}}</div>
                    <div class="font-size-12 text-center">{{$company_address}}</div>
                    </div>
                </td>
                <td style="width: 277px;vertical-align: top;border: 1px black solid">
                    <span class="font-size-8 " >Check Type of Mail service:</span><br />
                    <table class="checks" style="font-size: 0.7em">
                        <tr>
                            <td><div class="square">X</div></td>
                            <td>Certified</td>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Recorded Delivery (International)</td>     
                        </tr>
                        <tr>
                            <td><div class="square">&nbsp;</div></td>
                            <td>COD</td>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Registered</td>
                        </tr>
                        <tr>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Delivery Confirmation</td>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Return Receipt Merchandise</td>
                        </tr>
                        
                        <tr>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Express Mail</td>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Signature Confirmation</td>
                        </tr>
                        <tr>
                            <td><div class="square">&nbsp;</div></td>
                            <td>Insured</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    
                </td>
                <td style="width: 600px;vertical-align: top;border: 1px black solid">
                    <div class="font-size-8 ">Affix Stamp Here</div>
                    <div class ="font-size-6" style="font-style: italic; line-height: 12px;; margin-top: 3px;width: 93px">
                        (if issued as a certificate of mailing, or for additional copies of this bill)
                    </div>
                    <div class="font-size-8" style="width: 93px;font-style: italic; line-height: 12px;">Postmark and Date of Receipt</div>
                </td>
            </tr>
        </tbody>
    </table>
    
               
    <table class="recipients">
        <thead>
        <tr class="column-titles">
            <th class="text-center" style="width: 27px">Line</th>
            <th class="text-center" style="width: 156px">Article Number</th>
            <th class="text-center" style="width: 218px">Name of Addressee, Street, and PO Address</th>
            <th class="text-center" style="width: 54px">Postage</th>
            <th class="text-center" style="width: 55px">Fee</th>
            <th class="text-center" style="width: 50px">Handling Charge</th>
            <th class="text-center" style="width: 69px">Actual Value if Registered</th>
            <th class="text-center" style="width: 53px">Insured Value</th>
            <th class="text-center" style="width: 63px">Due Sander if COD</th>
            <th class="text-center" style="width: 33px">DC Fee</th>
            <th class="text-center" style="width: 33px">SC Fee</th>
            <th class="text-center" style="width: 33px">SH Fee</th>
            <th class="text-center" style="width: 33px">RD Fee</th>
            <th class="text-center" style="width: 33px">RR Fee</th>
            <th class="text-center" style="width: 120px;">Remarks</th>
        </tr>
        </thead>
        <tbody>
           @foreach ($certified_summary as $line)
            <tr class="column-titles">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-left">C/N: {{$line['barcode']}}</td>
                <td class="text-left">{{$line['sent_to']}}<br />{!! $line['sent_to_address']!!}</td>
                <td class="text-right">$ {{number_format($line['postage'],2)}}</td>
                <td class="text-right">$ {{number_format($line['fee'],2)}}</td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-center"> </td>
                <td class="text-left">
                    nto: {{$line['notice_id']}}<br />
                    Batch #: {{ $line['batch']}}<br />
                    Copy: {{ $line['copy_type']}}
                </td>
            </tr>
            @endforeach
           
            
            
        </tbody>
    </table>
    
    
   
                   
    <table class="footer" style="height: 78px; border: 1px solid black; border-collapse: collapse;">
        <tr>
            <td style="width: 88px; padding-top: 0.3cm">
                Total Number of Pieces Listed by Sender
                <div style="padding-top: 5px; font-size: 2em; text-align: center">{{ count($certified_summary)}}</div>
            </td>
            <td style="width: 87px; padding-top: 0.3cm">
                Total Number of Pieces Received as Post Office
            </td>
            <td style="width: 280px; padding-top: 0.3cm">
                Postmaster, Per <span style="font-style: italic">(Name of receiving employee)</span>
            </td>
            <td style="width: 600px;font-size:0.80em; padding:5px ">
                The full declaration of value is required on all domestic and international
                Registered Mail. The maximum indemnity payable for the reconstruction of
                nonnegotiable documents under Express Mail document reconstruction
                insurance is $500 per piece subject to additional limitations for multiple pieces
                lost or damaged in a single catastropic occurrence. The maximum indemnity
                payable on Express Mail merchandise is $500, but optional Express Mail
                Service merchandise insurance is available for up to $5,000 to some,but not all
                countries. The maximum indemnity payable is $25,000 for Registered Mail sent
                with optional postal insurance. See the DMM R900, S913, and S921 for
                limitations of coverage on insured and COD mail. See the IMM for limitations of
                coverage on international mail. Special handling charges apply only to Parcel
                Service parcels (A) and Standard Mail (B) parcels.
            </td>
        </tr>

    </table>
    <table class="foot-line" style="width:100%; border: none">
            <tr >
                <td style="text-align: left; width: 33%; border: none">
                        PS Form3877
               </td>
               <td style="text-align: center; width: 33%; border: none">
                        Complete by Typewriter,Ink, or Ball Point Pen
               </td>
               <td style="text-align: right; width: 33%; border: none">
                        Page 1 of 1
               </td>
                   
            </tr>
     </table>
</div>
@endsection