@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection
@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #job_filter{
        width: 400px !important;
    }
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
    
     #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    .pull-right{
        padding-right: 5px;
    }
    
    input[name="daterange"] {
            min-width: 180px;
    }
    input[type="checkbox"]{
        margin-top: -14px !important;
    }
    .print_invoicestitle{ display: none; }
    @media print {
     .page-header{ display: none; }
     #filters-form{ display: none; }
     .non-print{ display: none !important; }
     .print_invoicestitle{ display: block; } 
     .table{width: 500px !important;}
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row print_contents">
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12  col-md-12">
                            <h1> Invoices</h1>
                             
                            <div class="pull-right">
                            
                             @if(count($invoices) > 0 )
                             <button class="btn btn-success btn-pay" type="button" form="pay-form"> <i class="fa fa-money"></i>&nbsp;&nbsp; Pay Selected</button> 
                             @endif
                            </div>
                            <div class="pull-right">
                                <a href="{{ route("client.invoices.invoiceforbatch") }}" class="btn btn-warning"><i class="fa fa-arrow-right"></i> Generate Batch Invoice </a> 
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-danger btn-print btn-block form-control"> <i class="fa fa-print"></i> &nbsp; Print</button>
                            </div>
                        </div>
                        
                        
                    </div>
                         <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'client.invoices.setfilter', 'class'=>'form-inline'])!!}
                               
                                <div class="form-group">
                                    <label for="job_type_filter"> Job: </label>
                                    {!! Form::select('job',$jobs,session('invoice_filter.job'),['class'=>'form-control','id'=>'job_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_status_filter"> Status: </label>
                                    {!! Form::select('status',$statuses,session('invoice_filter.status'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_amount_filter"> Amount: </label>
                                    {!! Form::text('amount',session('invoice_filter.amount'),['class'=>'form-control'])!!}
                                </div>
                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('client.invoices.resetfilter') }}" class="btn btn-danger">Clear</a>
                            {!! Form::close() !!}
                           
                        </div>
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                         {!! Form::open(['route' => 'client.invoices.payment', 'class'=>'form-inline', 'id' => 'pay-form'])!!}
                        @if(count($invoices) > 0 )
                        <div class="col-xs-12">
                        <center><h1 class="print_invoicestitle"> Invoices</h1></center>    
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    
                                    <th>Work Order Number</th>
                                    <th>Job Number</th>
                                    <th>Job Name</th>
                                    <th>Job Address</th>
                                    <th>Customer Name</th>
                                    <th>Entered Date</th>
                                    <th>Completed Date</th>

                                    <th class="non-print">Print Notice</th>

                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="non-print">Action</th>
                                    <th class="non-print col-xs-1">Pay All
                                        <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input id="select_all"  type="checkbox" class="selection"><span></span>
                                        </label>
                                            
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td> {{ $invoice->number  }}</td>
                                   
                                    <td> 
                                        @if ($invoice->work_order)   
                                        <a href="{{route('client.notices.edit',$invoice->work_order->id)}}" class="non-print"> {{$invoice->work_order->number}} </a>
                                        <p class="print_invoicestitle">{{$invoice->work_order->number}}</p>

                                        @else
                                        N/A
                                        @endif

                                    </td>
                                    
                                    <td> {{ $invoice->work_order ?  $invoice->work_order->job->number : 'N/A' }}</td>
                                    <td> {{ $invoice->work_order ?  $invoice->work_order->job->name : 'N/A' }}</td>
                                    <td> {!! $invoice->work_order ?  nl2br($invoice->work_order->job->full_address_no_country) : 'N/A' !!}</td>
                                    <td>
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
                                    <td>{{date('m/d/Y', strtotime($invoice->work_order->created_at))}}</td>
                                    <td>
                                        @if ($invoice->work_order->status=='completed')
                                        {{date('m/d/Y', strtotime($invoice->work_order->updated_at))}}
                                        @endif
                                    </td>
                                    <td class="non-print"> 
                                        @if($invoice->status == "paid" ||  $invoice->client->billing_type == "invoiced")
                                            @if (count($invoice->work_order->attachments)>0)
<?php 
$attach_id=null;
foreach ($invoice->work_order->attachments as $attach ) {
     
    if($attach->recipient['party_type'] =="customer" ){
            $attach_id=$attach->id;
    }
}
if (!$attach_id){
foreach ($invoice->work_order->attachments as $attach ) {    
    if($attach->type == 'generated'){
            $attach_id=$attach->id;break;
    }
}
}

if ($invoice->client->billing_type !== 'invoiced'){
    foreach ($invoice->work_order->invoices as $wo_invoices ) {
        if ($wo_invoices->status == "open" || $wo_invoices->status == "unpaid"){
            $attach_id=null;break;
        }
    }
}
//echo $attach_id;
if ($attach_id){

?>
                                            
                                                <a class="btn btn-xs btn-warning" href="{{route('client.attachment.printnotice',base64_encode($attach_id))}}"> <i class="fa fa-print"></i> Print Notice</a>
<?php } ?>
                                            @endif
                                        @endif
                                    </td>
                                    <td> {{ $invoice->total_amount }}</td>
                                    
                                    @if($invoice->status == "unpaid")
                                        @if($invoice->client->billing_type == "invoiced") 
                                        <td> {{ $invoice->status }}</td>
                                        @else 
                                        <td>payment pending</td>
                                        @endif
                                    @else

                                        <td> {{ $invoice->status }}
                                            <p>
                                            <?php
                                            $pm=\App\Payment::where('id',$invoice->payment_id)->first();
                                            $p_type=[
                                                'credit_card'=>'Credit Card',
                                                'pay_by_check'=>'Check'

                                            ];

                                            if (count($pm)>0){
                                                echo "(".$p_type[$pm->type].")";
                                            }
                                            ?>
                                        </p>

                                        </td>
                                    @endif
                                    
                                    <td class="non-print">
                                        <a class="btn btn-success btn-xs btn-view" href="{{ route('client.invoices.show',$invoice->id)}}"><i class="fa fa-eye"></i>View</a>
                                    </td>
                                    <td class="non-print">
                                        @if($invoice->status== 'open' || $invoice->status== 'unpaid')
                                        @if(!$invoice->batch_id)
                                        <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="pay[{{$invoice->id}}]"  type="checkbox" class="selection"><span></span>
                                        </label>
                                        </div>
                                        @endif
                                        @endif
                                        @if($invoice->batch_id)
                                        <div>
                                            &nbsp;<a href="{{ route('client.invoicesbatches.printview',$invoice->batch_id)}}">Batched</a>
                                            <a href="{{ route('client.invoicesbatches.payment',$invoice->batch_id)}}" class="btn btn-xs btn-warning">Pay batch</a>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $invoices->links() }}
                        </div>
                        {{Form::close()}}
                        @else
                        <div class="col-xs-12">
                            <h5>No Invoices found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script>
$('.btn-pay').click(function(){
    $('#pay-form').submit();
    $('.btn-pay').addClass("disabled");
    $('.btn-pay').css('pointer-events','none');
}); 
$('.btn-print').click(function(){
     $('table').removeClass('table');
    $(".print_contents").show();
    window.print();
    $('table').addClass('table');
});
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $("#job_filter").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/jobs/joblist")}}',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove();   
                return {
                    results: $.map(data, function (item) {
                        return {
                            name: item.name,
                            id: item.id,
                            number: item.number,
                            text: item.name,
                        }
                    })
                };
            }
        },
        templateResult: formatJob
    });
    function formatJob (job) {
        var str = '<span>' + job.name + '</span>';
        var $state = $(str);
        return $state;
    }
    $('#select_all').change(function() {
        var checkboxes = $('.selection');
        if($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
   
});
</script>
@endsection