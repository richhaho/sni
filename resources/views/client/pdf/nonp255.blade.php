@section('css')


<style type="text/css">

 @media print {
    #page {

        font-size: 12pt !important;
        
    }
    
    .small {
    font-size: 10pt !important;
  
    }
    .bold {
        font-size:13pt !important;
        font-weight: bold;
    }
    
     .warning {
        font-size: 12pt!important;
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
        font-size: 13pt !important;
        font-weight: bold;
    }
    .warning {
        font-size: 12pt;
        font-weight: bold;
    }
}
</style>

@append

<div id="page">
    <div class="content">
    <h1 class="text-center">@if(isset($has_amended)) AMENDED @endif NOTICE OF NONPAYMENT</h1>
    <h5 class="text-center">255.05</h5>
    <br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 20%">To Contractor</td>
                <td>{{ $gc_firm_name }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 20%">Address :</td>
                <td>{!! $gc_address !!}</td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
        @if(isset($sureties))
        @foreach($sureties as $surety)
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 20%">To Surety</td>
                <td>{{ $surety['name'] }} </td>
            </tr>
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 20%">Address :</td>
                <td>{!! $surety['address'] !!}</td>
            </tr>
        @endforeach
        @endif
        </tbody>
    </table>
    <p class="text-justify">The undersigned  claimant notifies you that: <br> Claimant has furnished {!! $materials !!}  for the improvement of the real property identified as:</p>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%; vertical-align: top;" class="warning">
               {{ $job_name }}<br>
               {!! $job_address !!}<br>
            </td>
            <td style="width: 50%; vertical-align: top;">
                @if($project_number)
                <span class="warning">PROJECT# {{$project_number}}</span><br>
                @endif
                @if($nto_number)
                <span class="warning">JOB# {{$nto_number}}</span><br>
                @endif
            </td>
        </tr>
    </table>     
    <p>
        The corresponding amount now due and unpaid is ${{$amount_due ? number_format($amount_due,2):'_______'}}. 
        @if($retainage) 
        The corresponding amount now due includes retainage in the amount of ${{number_format($retainage,2)}}. 
        @else 
        The corresponding amount does not include retainage. 
        @endif 
        @if($interest_amount_due) 
        The Corresponding amount due includes interest (per contract) in the amount of ${{number_format($interest_amount_due,2)}}. 
        @endif
        <br>
        Claimant has been paid on account to date in the amount of ${{$amount_paid ? number_format($amount_paid,2):'___________'}} for previously furnishing  {!! $materials ? $materials:'________________' !!} for this improvement. 
        <br> 
        @if($continue_furnishing=='Yes') Claimant expects to continue furnishing  for this improvement in the future and the corresponding amount expected to become due is ${{$expected_amount_due ? number_format($expected_amount_due,2):'________'}}.
        @else
        Claimant does not expect to furnish any additional materials or services under this contract as the last day of furnishing was {{ date_format(date_create($last_day_on_job ),'F jS, Y')}}.
        @endif 
        <br> 
        @if($include_soa_line_items=='Yes')
        <table style="width: 90%;">
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   </td>
                <td style="font-weight: bold;width:40%">Total amount of the contract:</td>
                <td align="right">${{number_format($total_contract_amount,2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp; </td>
                <td style="font-weight: bold;width:40%">Total amount of Change Orders:</td>
                <td align="right">${{number_format($total_changeorder_amount,2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp; </td>
                <td style="font-weight: bold;width:40%">Net amount of contract:</td>
                <td align="right">${{number_format($total_contract_amount+$total_changeorder_amount,2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
            </tr>
            <tr>
                <td> &nbsp;&nbsp;</td>
                <td style="font-weight: bold;width:40%">Total amount invoiced:</td>
                <td align="right">${{number_format($total_invoice_amount,2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp; </td>
                <td style="font-weight: bold;width:40%">Total paid to date:</td>
                <td align="right">${{number_format($total_paid_date,2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td style="font-weight: bold;width:40%">Amount due at this time:</td>
                <td align="right">${{number_format(abs($total_invoice_amount-$total_paid_date),2)}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
            </tr>
        </table>

        @endif
        @if($customer_working_for_contract=='Yes')
        Claimant was in privity with the Contractor.
        @else
        If the claimant was not in privity with the Contractor then the Notice to Owner/Notice to Contractor was sent on {{$notice_date ? date_format(date_create($notice_date ),'F jS, Y'):'___________'}} by certified mail. @if ($certified_number) ({{$certified_number}}) @endif
        @endif
        <br>
        Under penalties of perjury, I declare that I have read the foregoing Notice of Nonpayment and that the facts stated in it are true. 
    </p>
    <p>Dated on {{$dated_on ? date_format(date_create($dated_on ),'F jS, Y'):'____________________'}}</p>

    <table  style="width: 100%">
        <tr>
            <td style="width:50%">
                &nbsp;
            </td>
            <td style="width:50%">
                <div class="esignature" style="width:100%; height: 30px"></div>
                <div>
                By:_______________________________________<br>
                {{$client_name}}, {{$client_title}}<br>
                {{$client_company_name}}<br>
                {!! preg_replace('/\<br(\s*)?\/?\>/i', "<br>", $client_address) !!}<br />
                {{ $client_email }}<br>
                {{ $client_phone }}
                </div>
            </td>
        </tr>
    </table>
    <p>Sworn to and subscribed before me this {{$sworn_at ? date_format(date_create($sworn_at ),'F jS, Y'):'__________'}} by means of ____ physical presence or ____ online notarization by {{$client_name ? $client_name:'___________' }} as {{$client_title ? $client_title:'___________'}} of {{$client_company_name ? $client_company_name:'___________'}} who is personally known to me or produced as identification, and did take an oath.</p>
    <br>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%">
            <td style="width: 50%">
                <div style="width:100%; border-bottom: 1px solid black;" class="text-center small"><br></div>
                <p class="small"> Notary Public - My Commission Expires:</p>
                <div style="width:100%; border-bottom: 1px solid black" class="text-center small"><br></div><br>
                <p class="small">Print,Type or Stamp Commissioned Name of Notary Public </p>
            </td>
        </tr>
    </table>  

    <p class="warning">Copies sent by Certified Mail - Return Receipt and Regular Mail:
    </p>
    @if(isset($parties))
    @foreach($parties as $gc)
        {{ $gc['company_name']}}<br>
    @endforeach
    @endif
    <br>
    @if($customer_working_for_contract!='Yes')
    The Claimant has also included the following document(s) to substantiate the amount claimed as unpaid in the notice:<br>
        @if(isset($documents_list))
        @foreach($documents_list as $doc)
            {{ $doc['document_name']}}<br>
        @endforeach
        @endif
    @endif
    <br>
    </div>
</div>