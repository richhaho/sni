@extends('client.pdf.pdf')
@section('content')
<div id="page">
    <div class="content">
        <div class="disclaimer">
            @if($barcode <> '') 
                <div class="barcode"> 
                   <div class="text-center">
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, "I25+",1.38,75) }}" alt="barcode"/>
                   </div>
                   <div class="number text-center">
                       {{ $barcode }}
                   </div>
               </div>
             <div class="mailing-address">
             @else
                 <div class="mailing-address-no-barcode">
            @endif
            
                 {!! $mailing_address !!}
             </div>
        </div>
        <div class="at-bottom">
        <p class="text-justify">IT IS OUR PLEASURE TO INFORM YOU THAT {{ $client_company_name }} QUALITY PRODUCTS AND/OR SERVICES HAVE BEEN SELECTED TO BE USED FOR THE IMPROVEMENT OF YOUR PROJECT. THIS NOTICE TO OWNER IS BEING FURNISHED IN COMPLIANCE WITH FLORIDA STATUTES, CONSTRUCTION LIEN LAW, CHAPTER 713, PART 1 AND IS NOT INTENDED TO REFLECT NEGATIVELY ON THE CHARACTER, CREDIT-WORTHINESS OR WORKMANSHIP OF ANY INDIVIDUAL OR COMPANY ASSOCIATED WITH THEIR MATERIALS AND/OR SERVICES. THIS NOTICE IS REQUIRED BY LAW TO BE SENT AS A PRELIMINARY MEASURE AND DOES NOT AUTOMATICALLY INDICATE THAT THERE IS ANY CREDIT PROBLEM. THIS NOTICE IS NOT A LIEN, CLOUD NOR ENCUMBRANCE UPON TITLE TO YOUR PROPERTY; NOR IS IT A MATTER OF PUBLIC RECORD.</p>
        <p class="text-justify">{{ $client_company_name }} HEREBY DEMANDS A VERIFIED COPY OF ANY PROVISION OF THE LEASE THAT PROHIBITS LIABILITY FOR THE IMPROVEMENTS BEING MADE BY THE LESSEE (WHERE APPLICABLE).  PLEASE FURNISH THE VERIFIED COPY (IN COMPLIANCE WITH §92.525 FLORIDA STATUTES OF LEASE PROVISION TO THE UNDERSIGNED BY FAX TO:  {{ $client_fax }}, BY EMAIL TO: {{ $client_email }} OR BY CERTIFIED MAIL TO: {{ $client_name }} {{ preg_replace('/\<br(\s*)?\/?\>/i', " ", $client_address) }}.  IF YOU FAIL TO SERVE A VERIFIED COPY OF THE LEASE PROVISION WITHIN 30 DAYS OF THIS DEMAND, OR SERVE A FALSE OR FRAUDULENT COPY, YOUR INTEREST AS LESSOR SHALL BE SUBJECT TO A CONSTRUCTION LIEN UNDER CHAPTER 713, PART 1 FLORIDA STATUTES IN FAVOR OF THE UNDERSIGNED.</p>
        <p class="text-justify"><b>THIS NOTICE IS BEING PREPARED USING ONLINE SOFTWARE SUPPLIED BY SUNSHINE NOTICES INC.  IF YOU WOULD LIKE TO USE THIS SERVICE, PLEASE VISIT WWW.SUNSHINENOTICES.COM FOR MORE INFORMATION ON THIS AS WELL AS OTHER SERVICES.</b></p>
        </div>
    </div>
</div>
@endsection