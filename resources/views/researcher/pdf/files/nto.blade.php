@extends('researcher.pdf.pdf')
@section('content')
<div id="page">
    <div class="content">
    <h1 class="text-center">NOTICE TO OWNER/NOTICE TO CONTRACTOR</h1>
    <h2 class="text-center">(PRELIMINARY NOTICE, FS SEC 713.06 NOTICE OF INTENT TO CLAIM AGAINST BOND FS 713)</h2>
    <h3 class="text-center">
        {{$client_company_name}}
        <span style="float: left;">DATE: {{ $nto_date }}</span>
        
        <span style="float: right;">Notice # {{ $nto_number }}</span>
    </h3>


    <p class="warning text-justify">WARNING! FLORIDA’S CONSTRUCTION LIEN LAW ALLOWS SOME UNPAID CONTRACTORS, SUBCONTRACTORS, AND MATERIAL SUPPLIERS TO FILE LIENS AGAINST YOUR PROPERTY EVEN IF YOU HAVE MADE PAYMENT IN FULL.  UNDER FLORIDA LAW, YOUR FAILURE TO MAKE SURE THAT WE ARE PAID MAY RESULT IN A LIEN AGAINST YOUR PROPERTY AND YOUR PAYING TWICE.  TO AVOID A LIEN AND PAYING TWICE, YOU MUST OBTAIN A WRITTEN RELEASE FROM CLIENT EVERY TIME YOU PAY YOUR CONTRACTOR.</p>
    <div class="row">
    <div class="rounded-box col-4 text-center">FEE SIMPLE LANDOWNER</div>
     @if(strlen($leaseholders[0]['full_name']) > 0)
        <div class="rounded-box col-8 text-center">LEASE HOLDER/TENANT</div>
    @else
        <div class="col-8 text-center"></div>
    @endif
    </div>
    <div class="row">
    <div class="col-4 ">
       {{  $land_owner_firm_name }}<br>
       {!! $land_owner_address !!}<br>
      
    </div>
        <div class=" col-8">
           
            <table  style="width: 100%">
                @if($leaseholders)      
                <tr>
             
            @foreach($leaseholders as $ls) 
            
            <td>
                {{ $ls['full_name'] }}<br>
                {!! $ls['address'] !!}<br>
               
            </td>
            
            @endforeach
            </tr>
            @endif
            </table>
        </div>
    </div>
    <h3 class="text-center">IMPORTANT INFORMATION FOR YOUR PROTECTION</h3>
    <p class="warning text-justify">UNDER FLORIDA’S LAWS, THOSE WHO WORK ON YOUR PROPERTY OR PROVIDE MATERIALS AND ARE NOT PAID HAVE A RIGHT TO ENFORCE THEIR CLAIM FOR PAYMENT AGAINST YOUR PROPERTY.  THIS CLAIM IS KNOWN AS A CONSTRUCTION LIEN.  IF YOUR CONTRACTOR FAILS TO PAY SUBCONTRACTORS OR MATERIAL SUPPLIERS OR NEGLECTS TO MAKE OTHER LEGALLY REQUIRED PAYMENTS, THE PEOPLE WHO ARE OWED MONEY MAY LOOK TO YOUR PROPERTY FOR PAYMENT, EVEN IF YOU HAVE PAID YOUR CONTRACTOR IN FULL.</p>
    <h3 class="text-center">PROTECT YOURSELF:</h3>
    <p class="warning text-justify">RECOGNIZE THAT THIS NOTICE TO OWNER MAY RESULT IN A LIEN AGAINST YOUR PROPERTY UNLESS ALL THOSE SUPPLYING A NOTICE TO OWNER HAVE BEEN PAID.  LEARN MORE ABOUT THE CONSTRUCTION LIEN LAW, CHAPTER 713 PART 1 FLORIDA STATUTES AND THE MEANING OF THIS NOTICE BY CONTACTING AN ATTORNEY OR THE FLORIDA DEPARTMENT OF BUSINESS AND PROFESSIONAL REGULATION.</p>
    <h3>{{$client_company_name}} HEREBY INFORMS YOU THAT HAS FURNISHED OR IS FURNISHING SERVICES OR MATERIALS AS FOLLOWS:</h3>
    <p class="warning" style="font-size:1.5em">{!! $materials !!}</p>
    <h3>UNDER AN ORDER GIVEN BY: {{ $customer_name }}</h3>
    <div class="row">
        <div class="rounded-box col-12 text-center">FOR THE IMPROVEMENT OF THE REAL PROPERTY IDENTIFIED AS:</div>
        </div>
        <div class="row">
        <table style="width: 100%">
            <tr>
                <td style="width: 35%">
                    {{ $job_name }}<br>
                    {!! $job_address !!}
                    {{ $job_county }}<br>
                  
                </td>
                <td>
                    @if($deed)
                        Deed #: {{ $deed }}<br>
                    @endif
                    @if($folio)
                        Parcel/Folio #: {{ $folio }} 
                    @endif
                    @if($noc)
                        &nbsp;NOC #: {{ $noc }}
                    @endif
                    @if($noc)
                        &nbsp;PJCT #: {{ $project_number }}
                    @endif
                       
                    @if($legal_description)
                        <br>Legal Description: {!! nl2br($legal_description) !!}
                    @endif
                    @if($job_county)
                        <br>{{ $job_county }} COUNTY
                    @endif
                </td>
            </tr>
        </table>

    </div>
    <p class="warning text-justify">FLORIDA LAW PRESCRIBES THE SERVING OF THIS NOTICE AND RESTRICTS YOUR RIGHT TO MAKE PAYMENTS UNDER YOUR CONTRACT IN ACCORDANCE WITH 713.06 FLORIDA STATUTES.  IF JOB IS BONDED UNDER SECTION 713.23 FLORIDA STATUTES, SECTION 255.05 FLORIDA STATUTES OR 270 USC THE FIRM SENDING THIS NOTICE WILL LOOK TO THE BOND (SURETY CO) FOR PROTECTION IF NOT PAID.  IF PAYMENT BOND EXISTS FURNISH A COPY TO THE UNDERSIGNED OR PROVIDE NAME AND ADDRESS OF BONDING COMPANY. FAILURE TO PROVIDE THIS INFORMATION MAY RENDER YOU LIABLE FOR DAMAGES.</p>
    <div class="row">
        <div class="col-6">
            <h3>ADDITIONAL COPIES FURNISHED TO THE FOLLOWING:</h3>
        </div>
        <div class="col-6">
            <h3>REQUEST FOR SWORN STATEMENT OF ACCOUNT MUST BE ADDRESSED TO:</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
                <table style="width: 100%">
                    <thead>
                        <tr>
                            <th style="text-align: left;">TYPE</th>
                            <th style="text-align: left;">NAME</th>
                        </tr>
                    </thead>
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
            </div>
        <div class="col-6">
            <div class="row">
            <div class="col-12 ">
                {{ $client_company_name }} @if(strlen($client_name)) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ATTN: {{ $client_name}} @endif<br>
                {{ preg_replace('/\<br(\s*)?\/?\>/i', " ", $client_address) }}<br>
                PHONE: {{  $client_phone }} @if(strlen($client_fax)) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FAX: {{ $client_fax}} @endif
            </div>
                </div>
            <div class="row">
                <div class="col-6 signature" style='border-top:  1px solid black'>
                    {{$client_title}}
                </div>
            </div>
        </div>
    </div>
    
    </div>
</div>
@endsection