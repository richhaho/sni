@section('css')


<style type="text/css">

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
     .content{
        min-height: 12in;
    }    
}

@media screen {
    #page {

        font-size: 11pt !important;

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
        <p>&nbsp;</p>
    <p>&nbsp;</p>
   <h2 class="text-center">NOTICE OF CONTEST OF CLAIM AGAINST PAYMENT BOND</h2>
   <p>&nbsp;</p>
   <p>To: {{ $lienor_company_name }}<br/>
       {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $lienor_address) !!}
   </p>
   <p>&nbsp;</p>
    <p class="text-justify" >You are notified that the undersigned contests your notice of nonpayment, 
        dated {{ (strlen($notice_date)) ? Carbon\Carbon::parse($notice_date)->format('F jS, Y') : ''}},
         and served on the undersigned on {{ (strlen($served_date)) ? Carbon\Carbon::parse($served_date)->format('F jS, Y') : ''}} 
       , and that the time within which you may file suit to enforce your claim 
       is limited to 60 days from the date of service of this notice.</p>
    
    <p class="text-justify" >
    The claim of any lienor upon whom the notice is served and who fails to 
    institute a suit to enforce his or her claim against the payment bond within 
    60 days after service of the notice shall be extinguished automatically. 
    The contractor or the contractor’s attorney shall serve a copy of the notice 
    of contest to the lienor at the address shown in the notice of nonpayment or 
    most recent amendment thereto and shall certify to such service on the face 
    of the notice and record the notice. 
    </p>
    <p>&nbsp;</p>
    <p>DATED on  {{ $dated_on }} 
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <div class="esignature" style="width:100%; height: 50px"></div>
                <div>
                By:_______________________________________<br>
                {{ $client_company_name}}<br /> 
                {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
                {{ $client_phone}} <br />
                {{ $client_name }}<br />
                 

                </div>
            </td>
        </tr>
    </table>
    
    
    </div>
</div>