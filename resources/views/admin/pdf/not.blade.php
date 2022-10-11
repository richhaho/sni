@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 12pt !important;
        line-height: 13pt;
    }
    
    .small {
    font-size: 11pt !important;
    font-weight: bold;
    }
    .bold {
        font-size: 12pt !important;
    font-weight: bolder;
    }
     .content{
        min-height: 12in;
    }
    .underwords{
        font-size: 9pt !important;
        border-top: 1px solid black;
    } 
}

@media screen {
    #page {

        font-size: 10pt !important;
        line-height: 13pt;
    }
    
    .small {
    font-size: 7pt !important;
  
    }
    .bold {
        font-size: 11pt !important;
    font-weight: bold;
    }
    .underwords{
        font-size: 7pt !important;
        border-top: 1px solid black;
    } 
}
</style>

@append
<div id="page">
    <div class="content">
   <h3 class="text-left">PREPARED BY/RETURN TO:<br>
            {{ $client_name }}</h3>
    <p>{{$client_company_name}}<br>
    {!!$client_address!!}<br>
    {{$client_phone}}</p>
    <p>Permit No. {{$permit}}<br>
       Tax Folio No. {{ $folio }} 
    </p>
    <h2 class="text-center">NOTICE OF TERMINATION</h2>
     <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$job_county}}</h3>
    <div>&nbsp;</div>
   
    <p class="text-justify">
       The Undersigned hereby give Notice of Termination of the Notice of Commencement record on @if($noc_recorded) {{date_format(date_create($noc_recorded),'F jS, Y')}}, @else _________ @endif in Official Records {{$book_page}} of the Public Records of @if($job_county) {{$job_county}} @else _____________ @endif County, Florida. 
    </p>
    <ol type="1">
        <li>
            Description of property: @if($job_legal_description) {{$job_legal_description }}, @endif 
            @if($job_name) {{$job_name }}, @endif 


            {{ str_replace('<br>', ' ', $job_address) }}
        </li>
        
        <li>General description of improvement: {{str_replace('<br>', ' ',str_replace('<br />', ' ',$improvements))}}</li>
        
        <li>Owner information or Lessee information if the Lessee contracted for the improvement:  
            <br>a. Name and address: {{str_replace('<br>', ' ',str_replace('<br />', ' ',$OwnerLessee_NameAddress))}}  
            <br>b. Interest in property: {{$Interest_Property}}  
            <br>c. Name and address of fee simple titleholder (if different from Owner listed above): {{str_replace('<br>', ' ',str_replace('<br />', ' ',$Simple_Titleholder))}}  
        </li>
        
        <li>a. Contractor: {{ $gc_firm_name}}, {{ $gc_name }}, {!! str_replace('<br>', ' ',str_replace('<br />', ' ',$gc_address))!!}  
            <br>b. Contractor’s phone number: {{ $gc_phone}}
        </li>
        
        <li>Surety (if applicable, a copy of the payment bond is attached):  
            
        

        @if(isset($noc_sureties))

            @foreach($noc_sureties as $surety)
                <br>a. Name and address:  {{str_replace('<br>', ' ',str_replace('<br />', ' ', $surety['name_address'] ))}}  
                <br>b. Phone number: {{ $surety['phone'] }}   
                <br>c. Amount of bond: ${{ $surety['amount'] }}    
            @endforeach
        @endif
        </li>

        <li>Lender:   
             
        @if(isset($noc_lenders))

            @foreach($noc_lenders as $lenders)
                <br>a. Lender’s Name and address:  {{str_replace('<br>', ' ',str_replace('<br />', ' ', $lenders['name_address'] ))}}  
                <br>b. Lender’s phone number:  {{ $lenders['phone'] }}   
            @endforeach
        @endif
        </li>
         
        <li>Persons within the State of Florida designated by Owner upon whom notices or other documents may be served as provided by Section 713.13(1)(a)7., Florida Statutes: 
            @if (isset($copiers_designated))
            @foreach($copiers_designated as $copiers)
                <br>a. Name and address:  {{str_replace('<br>', ' ',str_replace('<br />', ' ', $copiers['name_address'] ))}}  
                <br>b. Phone numbers of designated persons:  {{ $copiers['phone'] }}   
            @endforeach
            @endif

        </li>
        
        <li> In addition to himself or herself, Owner designates of to receive a copy of the Lienor’s Notice as provided in Section 713.13(1)(b), Florida Statutes.  
            @if (isset($othercopiers_designated))
            @foreach($othercopiers_designated as $copiers)
                <br>a. Name and address: {{str_replace('<br>', ' ',str_replace('<br />', ' ', $copiers['name_address'] ))}}  
                <br>b. Phone number of person or entity designated by owner:  {{ $copiers['phone'] }}   
            @endforeach
            @endif
        </li>
         
        <li>Expiration date of notice of commencement (the expiration date will be 1 year from the date of recording unless a different date is specified): {{ date_format(date_create($expiration_date ),'F jS, Y')}}</li>

        <li> Pursuant of Florida Statutes Section 713.132, the Notice of Commencement is terminated as of the day of @if($termination_date) {{ date_format(date_create($termination_date),'F jS, Y')}} @else _________ @endif (but no less than days after the Notice of Termination is recorded). </li>

        <li>This Notice of Termination applies to all real property subject to the Notice of Commencement except: {{$exempt_real_property}}</li>

        <li>To the best of Owner’s knowledge, all lienor’s have been paid in full. A copy of the Contractor’s Affidavit is attached. </li>

        <li> A copy of this document is being provided to the Contractor and to anyone who had provided Owner of its designee with a Notice to Owner as provided in Florida Statutes Section 713.06(2)(c)(d).</li>
        <br>


    </ol>
        
       <br>
     
    
    <br>    
    <p>
        <span class="underwords">(Signature of Owner or Lessee, or Owner’s or Lessee’s Authorized Officer/Director/Partner/Manager)  </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="underwords"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ( Print Name and Provide Signatory’s Title/Office)  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
        <br>
        The foregoing instrument was acknowledged before me this ______ day of ________________20,____________
         by means of __ physical presence or ___ online notarization by &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; as &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; for
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="underwords" style="padding-bottom: 10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Name of Person)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;&nbsp;&nbsp;&nbsp;
         <span class="underwords" style="padding-bottom: 10px"> &nbsp;&nbsp;(type of authority,...e.g. officer, trustee, attorney in fact)&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
         <span class="underwords" style="padding-bottom: 10px">&nbsp;&nbsp;(name of party on behalf of whom instrument was executed) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

         <br>
        Personally Known &nbsp;&nbsp; <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;&nbsp; Produced Identification  &nbsp;&nbsp; <i class="fa fa-square-o"></i> <br>
        Type of Identification ____________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notary Signature ___________________________________<br> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Print Name ___________________________________<br> 

    </p>


    </div>
</div>