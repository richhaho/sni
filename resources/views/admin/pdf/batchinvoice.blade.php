@section('css')

<style type="text/css">
    body{
        margin: 0px !important;
        padding: 0px !important;
    } 
    #page{
        margin: 0px !important;
        padding: 0px !important;
    } 
    .content{
        margin: 0px !important;
        padding: 0px !important;
    } 
    #page  h1 {
        display: block;
        font-size: 1.5em;
        margin-top: 0px;
        margin-bottom: 0px;
        font-weight: bold;
    }
@media print {
    body{
        margin: 0px !important;
        padding: 0px !important;
    }
    .content{
        margin: 0px !important;
        padding: 0px !important;
    } 
    #page {
        font-size: 11pt !important;
        margin: 0pt !important;
        padding: 0pt !important;
    }
    .small {
        font-size: 10pt !important;
        font-weight: bold;
    }
    .bold {
         font-size: 12pt !important;
        font-weight: bold;
    }
    .title {
         font-size: 14pt !important;
        font-weight: bold;
    }
    .noprint{
        display: none;
    }
    .active{
        background-color: #c0c0c0 !important;
    }
    .bordered{
        border: 1px solid black !important;
    }
    .bordered td{
        border: 1px solid black !important;
    }
    .bordered th{
        border: 1px solid black !important;
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
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td width="30%">
                        <img src="https://my.sunshinenotices.com/images/logo.png"> 
                    </td>
                    <td width="30%" class="title">
                         {{\App\CompanySetting::first()->name}}<br>
                         {!! nl2br(\App\CompanySetting::first()->address) !!}<br>
                         PHONE: 934.0970
                    </td>
                    <td width="40%">
                    </td>
                </tr>
            </tbody>
        </table>
        <p>&nbsp;</p>
         


        <table style="width: 100%" class="table text-center table-bordered bordered">
            <thead>
                <tr class="active bold text-center " >
                    <th width="100%" colspan="12"  style="text-align: left;">
                        <table style="width: 100%;border: none !important;" >
                            <thead>
                                <tr>
                                    <th width="5%" style="border: none !important;vertical-align: middle;">&nbsp; INV &nbsp;<br>&nbsp; TO: &nbsp;</th>
                                    <th class="bold" width="65%" style="border: none !important;vertical-align: middle;">
                                        {{$client->company_name}}<br>
                                        {{$client->address_1}} {{$client->address_2}}<br> 
                                        {{$client->city}}, {{$client->state}} {{$client->zip}}
                                    </th>
                                    <th class="bold" width="30%" style="text-align: center;border: none !important;vertical-align: middle;">
                                        INVOICE #B{{$batch->id}}<br>
                                        INVOICE PERIOD {{$period}}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </th>
                </tr>
                <tr class="active bold text-center " >
                    <th width="3%"  style="text-align: center;vertical-align: middle;">No.</th>
                    <th width="19%"  style="text-align: center;vertical-align: middle;">Customer Name</th>
                    <th width="5%"  style="text-align: center;vertical-align: middle;">Job Number</th>
                    <th width="15%"  style="text-align: center;vertical-align: middle;">Job Name</th>
                    <th width="20%"  style="text-align: center;vertical-align: middle;">Job Address</th>
                    <th width="4%"  style="text-align: center;vertical-align: middle;">Type</th>
                    <th width="5%"  style="text-align: center;vertical-align: middle;">Date</th>
                    <th width="4%"  style="text-align: center;vertical-align: middle;">WO#</th>
                    <th width="4%"  style="text-align: center;vertical-align: middle;">Inv#</th>
                    <th width="7%"  style="text-align: center;vertical-align: middle;">Fee</th>
                    <th width="7%"  style="text-align: center;vertical-align: middle;">Postage</th>
                    <th width="7%"  style="text-align: center;vertical-align: middle;">Total</th>
                      
                </tr>
            </thead>
            <tbody>
                <?php 
                    $no=0; $period_postage=0;
                ?>
                @foreach ($invoices as $invoice)
                <?php $no++; 
                    $postage=$invoice->total_amount-$invoice->lines()->where('description','not like','%MAIL%')->where('description','not like','%POSTAGE%')->where('description','not like','%CERTIFIED%')->where('description','not like','%PREVIOUS%')->sum('amount');
                        $period_postage+=$postage;
                ?>
                <tr>
                    <td style="text-align: center;vertical-align: middle;">{{$no}}</td>
                    <td style="text-align: left;vertical-align: middle;padding: 1px">
                        <?php 
                        $name='';
                        if ($invoice->work_order){
                        $customer=$invoice->work_order->job->parties->where('type','customer')->first();

                        if (count($customer)>0){
                            $customer_contact= $customer->contact;
                            if(count($customer_contact)>0){
                                $name=$customer->firm->firm_name;
                            };
                        }
                        }
                        echo $name;
                        ?>
                    </td>
                    <td style="text-align: center;vertical-align: middle;"> {{ $invoice->work_order ?  $invoice->work_order->job->number : 'N/A' }}</td>
                    <td style="text-align: left;vertical-align: middle;padding: 1px"> {{ $invoice->work_order ?  $invoice->work_order->job->name : 'N/A' }}</td>
                    <td style="text-align: left;vertical-align: middle;padding: 1px"> {{ $invoice->work_order ?  $invoice->work_order->job->address_1.' '.$invoice->work_order->job->address_2 : 'N/A' }}</td>
                    <td style="text-align: center;vertical-align: middle;">    @if ($invoice->work_order) 
                        @if ($invoice->work_order->type=='notice-to-owner')
                        NTO
                        @elseif ($invoice->work_order->type=='claim-of-lien')
                        COL
                        @elseif ($invoice->work_order->type=='satisfaction-of-lien')
                        SAT
                        @elseif ($invoice->work_order->type=='partial-satisfaction-of-lien')
                        PSAT
                        @elseif ($invoice->work_order->type=='notice-of-non-payment')
                        NNP
                        @else
                        @if(isset($invoice->work_order->order_type->name))
                            {{$invoice->work_order->order_type->name}}
                        @endif
                        @endif
                    @endif
                    </td>
                    <td style="text-align: center;vertical-align: middle;">@if ($invoice->created_at) {{date('m/d/Y', strtotime($invoice->created_at))}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;"> {{ $invoice->work_order ?  $invoice->work_order->id : '' }}</td>
                    <td style="text-align: center;vertical-align: middle;"> {{ $invoice->id  }}</td>
                    <td style="text-align: center;vertical-align: middle;">@if($invoice->total_amount-$postage) ${{number_format($invoice->total_amount-$postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">@if($postage) ${{number_format($postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">${{$invoice->total_amount}}</td>
                </tr>
                @endforeach
                 
                <tr>
                    <td colspan="4" rowspan="100" style="text-align: center;;vertical-align: middle;" class="title">EMAIL BILLING QUESTIONS TO:  Suzanne@sunshinenotices.com </td>
                    <td colspan="5" class="bold" style="text-align: center;vertical-align: middle;">TOTAL AMOUNT DUE THIS INVOICE PERIOD
                    </td>
                    <td style="text-align: center;vertical-align: middle;">@if($period_total-$period_postage) ${{number_format($period_total-$period_postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">@if($period_postage) ${{number_format($period_postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">${{number_format($period_total,2)}}</td>
                </tr>
                @if($past_total>0)
                <!-- <tr>
                    <td colspan="5" class="bold" style="text-align: center;vertical-align: middle;">*PAST DUE FROM INVOICE {{$first_batch_number}} FOR PERIOD ENDING {{$start_period}}
                    </td>
                    <td style="text-align: center;vertical-align: middle;">@if($past_total-$past_postage) ${{number_format($past_total-$past_postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">@if($past_postage) ${{number_format($past_postage,2)}} @endif</td>
                    <td style="text-align: center;vertical-align: middle;">${{number_format($past_total,2)}}</td>
                </tr> -->
                @endif

                @if(count($client->batch_invoices()->where('payed_at',null)->where('id','!=',$batch->id)->get())>0)
                <tr class="active bold text-center " >
                    <td colspan="12"> OUTSTANDING BATCH INVOICES</td>
                </tr>
                <tr class="active bold text-center " >
                    <td colspan="4" style="text-align: center;vertical-align: middle;"> Invoice Period
                    </td>
                    <td colspan="3" style="text-align: center;vertical-align: middle;"> Invoice #
                    </td>
                    <td></td>
                </tr>
           
                @foreach($client->batch_invoices()->where('payed_at',null)->where('id','!=',$batch->id)->get() as $un_batch)
                <tr>
                    <td colspan="4" style="text-align: center;vertical-align: middle;">{{date('m/d/Y',strtotime($un_batch->first_invoice()->created_at))}} - {{date('m/d/Y',strtotime($un_batch->created_at))}}</td>
                    <td colspan="3" style="text-align: center;vertical-align: middle;">{{$un_batch->id}}*</td>
                    <td>$ {{$un_batch->total_amount}}</td>
                </tr>
                @endforeach
           
                @endif
                 
            </tbody>
        </table>&nbsp;
         
        <p class="small" style="text-align: right;">* REFER TO PRIOR WEEKLY INVOICE FOR BREAKDOWN OF CHARGES</p>

        <table style="width: 100%">
            <tbody>
                <tr>
                    <td class="title" width="50%">
                        
                    </td>
                    <td width="23%"></td>
                    <td class="title" width="27%" style="text-align: center;border: 1px solid black;vertical-align: middle;">
                        TOTAL DUE UPON RECEIPT : ${{number_format($period_total+$past_total,2)}} 
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        

     
    </div>
</div>