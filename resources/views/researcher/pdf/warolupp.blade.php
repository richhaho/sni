@section('css')


<style type="text/css">
       #page  h1 {
        display: block;
        font-size: 1.5em;
        margin-top: 0px;
        margin-bottom: 0px;
        font-weight: bold;
    }
@media print {
#page {
    font-size: 14pt !important;

}
.small {
    font-size: 9pt !important;
 
}
.bold {
     font-size: 14pt !important;
    font-weight: bold;
}
}


@media screen {
    #page {

        font-size: 12pt !important;

    }
    
    .small {
    font-size: 9pt !important;
  
    }
    .bold {
        font-size: 12pt !important;
    font-weight: bold;
    }
}
</style>

@append

<div id="page">
    <div class="content">
        <p>&nbsp;</p>
    <p>&nbsp;</p>
   <h1 class="text-center">WAIVER AND RELEASE OF LIEN UPON PROGRESS PAYMENT</h1>
   <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned lienor, in consideration of the sum of $ {{ number_format($amount,2)}}, 
        hereby waives and releases its lien and right to claim 
        a lien for labor, services, or materials furnished through {{ $date_paid }}
        to {{ $customer_name }}, on the job of {{  $land_owner_firm_name }} to the 
        following property:</p>
    <p>&nbsp;</p>
    <p>
        {{ $nto_number }}<br>
        {{ $job_name }}<br>
        {!! $job_address !!}<br>
        {{ $job_county }}<br>
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify"> This waiver and release does not cover any retention or 
        labor, services, or materials furnished after date specified.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
     <p class="text-center " style="width:30%; border-top: 1px solid black">Signature</p>
    <p>
        Dated on {{ $dated_on }}<br />
        {{$client_company_name}}<br /> 
        {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
        {{ $client_name }}, {{$client_title}}<br />
        {{$client_email}}
    </p>
    
    </div>
</div>