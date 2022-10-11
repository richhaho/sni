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
    font-size: 15pt !important;

}
.small {
    font-size: 11pt !important;
 
}
.bold {
     font-size: 15pt !important;
    font-weight: bold;
}
 .warning {
         font-size: 15pt !important;
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
    .warning {
         font-size: 12pt !important;
    }

}
</style>

@append
<div id="page">
    <div class="content">
        <p>&nbsp;</p>
    <p>&nbsp;</p>
   <h1 class="text-center">CONDITIONAL WAIVER AND RELEASE OF LIEN UPON FINAL PAYMENT</h1>
   <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify">The undersigned lienor, in consideration of the Final payment in the sum 
        of $ {{ number_format($amount,2) }}, hereby waives and releases its lien and right to claim 
        a lien for labor, services, or materials furnished to {{ $customer_name }}, 
        on the job of {{  $land_owner_firm_name }} to the following property:</p>
    <p>&nbsp;</p>
    <p>
        {{ $job_name }}<br>
        {!! $job_address !!}<br>
        {{ $job_county }}<br>
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p class="text-justify "> If the consideration recited above is a check or draft, 
        this Waiver and Release of Lien Upon Final Payment is conditioned upon 
        payment of said check or draft; otherwise it is void.</p>
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