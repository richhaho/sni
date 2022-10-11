@section('css')
<style>
  @media print {
    .content{
        min-height: 11in;
    }    
    
    #page {
 
        font-size:10.5pt;
        line-height: 14pt;
        font-family: ArialNarrow, sans-serif;
    }
    
  } 
    
    
.warning {
    font-size: 10pt;
    font-weight: bold;
}
</style>
@stop
<div id="page">
    <div class="content">
    <p class="warning text-justify">WARNING! FLORIDA’S CONSTRUCTION LIEN LAW ALLOWS 
        SOME UNPAID CONTRACTORS, SUBCONTRACTORS, AND MATERIAL SUPPLIERS TO FILE LIENS 
        AGAINST YOUR PROPERTY EVEN IF YOU HAVE MADE PAYMENT IN FULL.  UNDER FLORIDA LAW, 
        YOUR FAILURE TO MAKE SURE THAT WE ARE PAID MAY RESULT IN A LIEN AGAINST 
        YOUR PROPERTY AND YOUR PAYING TWICE.  TO AVOID A LIEN AND PAYING TWICE, 
        YOU MUST OBTAIN A WRITTEN RELEASE FROM {{$client_company_name}} EVERY TIME YOU PAY YOUR CONTRACTOR.</p>
    <table style="width:100%">
        <tr>
            <td style="width:15%" class="text-left">DATE: {{ $nto_date }}</td>
            <td style="width:70%" class="text-center"><h1>NOTICE TO OWNER/NOTICE TO CONTRACTOR</h1></td>
            <td style="width:15%" class="text-right">Notice # {{ $wo_number }}</td>
        </tr>
        <tr>
            <td colspan ="3" class="text-center">
                <h3>(PRELIMINARY NOTICE, FS SEC 713.06 NOTICE OF INTENT TO CLAIM AGAINST BOND FS 713)</h3>
            </td>
        </tr>
    </table>
    
    

    
    <div class="row">
    <div class="col-4">FEE SIMPLE LANDOWNER</div>
     @if(strlen($leaseholders[0]['full_name']) > 0)
        <div class="col-8">LEASE HOLDER/TENANT</div>
    @else
        <div class="col-8 text-center"></div>
    @endif
    </div>
    <div class="row">
    <div class="col-4 ">
       {{  $land_owner_firm_name }}<br>
       {!! $land_owner_address !!}<br>
       @if(isset($land_owner_email))
       @if(strlen($land_owner_email) > 0 )
       Email: {{ $land_owner_email }}<br>
       @endif
       @endif
       @if(isset($land_owner_phone))
       @if(strlen($land_owner_phone) > 0 )
       Phone: {{ $land_owner_phone }}
       @endif
       @endif
    </div>
        <div class=" col-8">
           
            <table  style="width: 100%">
                @if($leaseholders)      
                <tr>
             
            @foreach($leaseholders as $ls) 
            
            <td>
                {{ $ls['full_name'] }}<br>
                {!! $ls['address'] !!}<br>
                @if(isset($ls['email']))
                @if(strlen($ls['email']) > 0 )
                Email: {{$ls['email'] }}<br>
                @endif
                @endif
                @if(isset($ls['phone']))
                @if(strlen($ls['phone']) > 0 )
                Phone: {{ $ls['phone'] }}
                @endif
                @endif
            </td>
            
            @endforeach
            </tr>
            @endif
            </table>
        </div>
    </div>
    <p><span class="warning">{{$client_company_name}}</span> HEREBY INFORMS THAT 
        THEY HAVE FURNISHED OR WILL BE FURNISHING SERVICES OR MATERIALS AS FOLLOWS:</h3>
    <p class="warning" >{!! $materials !!}</p>
    <div class="row">
        <div class="col-12">FOR THE IMPROVEMENT OF THE REAL PROPERTY IDENTIFIED AS:</div>
        </div>
        <div class="row">
        <table style="width: 100%">
            <tr>
                <td style="width: 35%; vertical-align: top;" class="warning">
                    {{ $job_name }}<br>
                    
                    {!! $job_address !!}
                   
                  
                </td>
                <td class="warning">
                    @if($deed)
                        Deed #: {{ $deed }}<br>
                    @endif
                    @if($folio)
                        Parcel/Folio #: {{ $folio }}<br>
                    @endif
                    @if(isset($noc))
                     @if($noc)
                        &nbsp;NOC #: {{ $noc }}
                    @endif
                    @endif
                    @if(isset($project_number))
                    @if($project_number)
                        &nbsp;PJCT #: {{ $project_number }}
                    @endif
                    @endif
                    @if($legal_description)
                    <br>Legal Description: <strong>{!! nl2br($legal_description) !!}</strong><br>
                    @endif
                    @if($job_county)
                        {{ $job_county }} COUNTY FLORIDA
                    @endif
                </td>
            </tr>
        </table>

    </div>
    <p>UNDER AN ORDER GIVEN BY: <span class="warning">{{ $customer_name }}<span><p>
     <p class="text-justify warning">FLORIDA LAW PRESCRIBES THE SERVING OF THIS NOTICE 
         AND RESTRICTS YOUR RIGHT TO MAKE PAYMENTS UNDER YOUR CONTRACT IN ACCORDANCE WITH 
         SECTION 713.06, FLORIDA STATUTES</p>
    <h3 class="text-center warning">IMPORTANT INFORMATION FOR YOUR PROTECTION</h3>
    <p class="text-justify">UNDER FLORIDA'S LAWS, THOSE WHO WORK ON YOUR PROPERTY 
        OR PROVIDE MATERIALS AND ARE NOT PAID HAVE A RIGHT TO ENFORCE THEIR CLAIM 
        FOR PAYMENT AGAINST YOUR PROPERTY.  THIS CLAIM IS KNOWN AS A CONSTRUCTION LIEN. 
        IF YOUR CONTRACTOR FAILS TO PAY SUBCONTRACTORS OR MATERIAL SUPPLIERS OR NEGLECTS 
        TO MAKE OTHER LEGALLY REQUIRED PAYMENTS, THE PEOPLE WHO ARE OWED MONEY MAY LOOK 
        TO YOUR PROPERTY FOR PAYMENT, EVEN IF YOU HAVE PAID YOUR CONTRACTOR IN FULL</p>
    <h3 class="text-center">PROTECT YOURSELF:</h3>
    <p class="text-justify">
    <ul>
        <li>RECOGNIZE THAT THIS NOTICE TO OWNER MAY RESULT IN A LIEN AGAINST 
            YOUR PROPERTY UNLESS ALL THOSE SUPPLYING A NOTICE TO OWNER HAVE BEEN PAID.</li>
        <li>LEARN MORE ABOUT THE CONSTRUCTION LIEN LAW, CHAPTER 713 PART 1 FLORIDA 
            STATUTES AND THE MEANING OF THIS NOTICE BY CONTACTING AN ATTORNEY OR THE 
            FLORIDA DEPARTMENT OF BUSINESS AND PROFESSIONAL REGULATION.</li>
    </ul>
    </p>

    <div class="row">
        <table style="width: 100%">
            <tr>
                <td style="width: 50%; padding-right: 30px;" valign="top">
                    <h3>ADDITIONAL COPIES FURNISHED TO THE FOLLOWING BY CERTIFIED AND/OR REGULAR MAIL:</h3>
                   
                    <table style="width: 100%">
                        <thead>
                            <tr>
                               <!-- <th style="text-align: left;width: 30%">TYPE</th>-->
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
                </td>
                <td style="width: 50%">
                    <div class="row">
                        <h3>REQUEST FOR SWORN STATEMENT OF ACCOUNT MUST BE ADDRESSED TO:</h3>
                    <div class="col-12 warning">
                        {{ $client_company_name }}<br>
                        @if(strlen($client_address)){!! $client_address !!}<br>@endif
                        @if(strlen($client_phone)) PHONE: {{  $client_phone }} @endif
                        @if(strlen($client_fax)) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; FAX: {{ $client_fax}} @endif
                    </div>
                        </div>
                     @if(strlen($signature)> 0) 
                    <div class="row" >
                        <div class="col-12"><img src="{{$signature}}" style="width:250px;"></div>
                    </div> 
                     @else
                        <div class="row" >
                          <div class="col-12" style="height: 40px;"></div>
                        <div class="col-12 text-center esignature" style="width:250px;">{{ $client_name }}</div>
                      </div> 
                    @endif
                   <div class="row" >
                        <div class="col-6 " style="border-top:  1px solid black; width:250px;">
                            {{$client_title}} <br>
                            {{ $client_name}}
                        </div>
                    </div>
                </td>
            </tr>
            <tr >
                <td colspan="2">
                
                    
                </td>
            </tr>
        </table>
        
    </div>
   
    </div>
     <div class="footer">
    <p class="text-justify">IF JOB IS BONDED UNDER SECTION 713.23 FLORIDA STATUTES, 
        SECTION 255.05 FLORIDA STATUTES OR 270 USC THE FIRM SENDING THIS NOTICE WILL 
        LOOK TO THE BOND (SURETY CO) FOR PROTECTION IF NOT PAID.  IF PAYMENT BOND 
        EXISTS FURNISH A COPY TO THE UNDERSIGNED OR PROVIDE NAME AND ADDRESS OF BONDING 
        COMPANY. FAILURE TO PROVIDE THIS INFORMATION MAY RENDER YOU LIABLE FOR DAMAGES.</p>

    </div>
</div>