@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 10pt !important;
        line-height: 13pt;
    }
    
    .small {
    font-size: 9pt !important;
  
    }
    .bold {
        font-size: 10pt !important;
    font-weight: bold;
    }
     .content{
        min-height: 10in;
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
    <p>Permit No__________________<br>
       Tax Folio No. {{ $folio }} 
    </p>
    <h2 class="text-center">NOTICE OF COMMENCEMENT</h2>
     <h3 class="text-left">STATE OF FLORIDA</h3>
    <h3 class="text-left">COUNTY OF {{$client_county}}</h3>
    <div>&nbsp;</div>
   
    <p class="text-justify">
       The undersigned hereby gives notice that improvements will be made to 
       certain real property situated in {{ $job_county }} County, Florida, and 
       in accordance with Chapter 713, Florida Statutes, the following information 
       is provided in this Notice of Commencement”
    </p>
    <ol type="a">
        <li>
            The property is described as follows: {{$job_name}}, {{ str_replace('<br />', ' ', $job_address) }}
        </li>
        <li>Said improvements shall generally consist of: {{ $improvements}}</li>
        <li>The name and address of the “owner” as defined in statutes 713.01(23) and its interest in the site is:<br>
            {{$land_owner_firm_name}}<br>
            {!!$land_owner_address!!}<br>
            {{$land_owner_name}}<br>
            Whose interest in property to be improve is:<br>
            
        </li>
        <li>The name,  address and phone number of the contractor is:<br>
            {{ $gc_firm_name}}<br>
            {!! $gc_address!!}<br>
            {{ $gc_phone}}<br>
            {{ $gc_name }}
        </li>
        <li>
            The name, address and phone number of surety on the payment bond 
            under FS 713.23, Florida Statutes is:<br>
            {{ $bond_firm_name }}<br>
            {!! $bond_address !!}<br>
            {{ $bond_name }}<br>
            {{ $bond_phone }}
        </li>
        <li>
            The name, address and phone number of any person making a loan for 
            the construction of the improvements is:<br>
            {{ $bank_firm_name }}<br>
            {!! $bank_address !!}<br>
            {{ $bank_name }}<br>
            {{ $bank_phone }}
        </li>
        <li>
            The name, address and phone numbers within the state of Florida of a 
            person other than the owner who may be designated by the owner as 
            the person upon whom notices of other documents may be served under 
            Chapter 713,  713.13(1)(a)7, Florida Statutes is:<br>
            {{ $copy_firm_name }}<br>
            {!! $copy_address !!}<br>
            {{ $copy_name }}<br>
            {{ $copy_phone }}
        </li>
        <li>
             Persons designated in addition to owner to receive copy of Lienor’s 
             notice as provided in 713.13(1)(b), Florida Statutes are:<br>
            {{ $copyl_firm_name }}<br>
            {!! $copyl_address !!}<br>
            {{ $copyl_name }}<br>
            {{ $copyl_phone }}

        </li>
        <li>
        Expiration date of notice of commencement (the expiration date is one 
        (1) year from the    date of recording unless a different date is 
        specified): ie Twenty-Four (24) months from the date of recording.
        </li>
       
    </ol>
    <div>&nbsp;</div>
    </div>
    <div class="footer">
    <p class="warning text-justify">
        WARNING TO OWNER: ANY PAYMENTS MADE BY THE OWNER AFTER THE 
            EXPIRATION OF THE NOTICE OF COMMENCEMENT ARE CONSIDERED IMPROPER 
            PAYMENTS UNDER CHAPTER 713, PART I, SECTION 713.13, FLORIDA STATUTES, 
            AND CAN RESULT IN YOUR PAYING TWICE FOR THE IMPROVEMENTS TO YOUR 
            PROPERTY.   A NOTICE OF COMMENCEMENT MUST BE RECORDED AND POSTED ON 
            THE JOB SITE BEFORE THE FIRST INSPECTION.  IF YOU INTEND TO OBTAIN 
            FINANCING, CONSULT WITH YOUR LENDER OR AN ATTORNEY BEFORE COMMENCING 
            WORK OR RECORDING THE NOTICE OF COMMENCEMENT. 
    </p>
    </div>
</div>