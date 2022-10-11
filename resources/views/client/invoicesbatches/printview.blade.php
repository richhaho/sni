@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .table > tbody > tr > .no-line {
      border-top: none;
  }

  .table > thead > tr > .no-line {
      border-bottom: none;
  }

  .table > tbody > tr > .thick-line {
      border-top: 2px solid;
  }

  @media print {
     .btn-print{ display: none; }
     .btn-back{ display: none; }
    }
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
                <div class="col-xs-12">
                    <br>
                    <div class="col-xs-12 pull-right">
                            <button type="button" class="btn btn-success btn-print pull-right"> <i class="fa fa-print"></i> &nbsp;&nbsp; Print&nbsp;&nbsp;&nbsp;&nbsp;</button>
                            <a style="margin-right: 5px" class="btn btn-danger pull-right btn-back" href="{{ route('client.invoicesbatches.index')}}"><i class="fa fa-arrow-left"></i> &nbsp;&nbsp;Back&nbsp;&nbsp;</a>
                    </div>
                </div>

            <div  class="col-xs-12">
                <div class="page-header">
                    <h2>Batch Invoice: #B{{$batch_id}}
                        
                    </h2>  
                    <h5 class="pull-right">{{\App\CompanySetting::first()->name}}<br>{!! nl2br(\App\CompanySetting::first()->address) !!}<br />Batch Date: {{$batch_date}}<br /><br>{{$client_company}}</h5>
                    <p>
                        <center><img src="/images/logo.png"></center>
                    </p>    
                                           
                </div>
                    
                     
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                
                @if (count($errors) > 0)
                    <div class="alert alert-danger">            
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class ="col-xs-12">
                    <div class="col-md-12 ">
                            <div class="panel panel-default print_contents">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Selected Invoices summary</strong></h3>
                                </div>
                                
                                <div class="panel-body">
                                    @if($todownload)
                                    <p>In order to be able to download the selected file you must pay the due invoices</p>
                                    @endif
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr>
                                                <td><strong>Item</strong></td>
                                                <td class="text-center col-xs-2"><strong>Price</strong></td>
                                                <td class="text-center"><strong>Quantity</strong></td>
                                                <td class="text-right"><strong>Totals</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($invoices as $invoice)    
                                            <tr> <td class="thick-line" colspan="4">&nbsp;</td></tr>
                                                <tr>
                                                    <td  colspan="4">Invoice ID: {{$invoice->id}}&nbsp;&nbsp;-&nbsp;&nbsp;Work Order Number: {{ $invoice->work_order->number}}</td>
                                                </tr>
                                                <tr>
                                                    <td  colspan="4">{{ $invoice->work_order->job->number}}&nbsp;&nbsp;-&nbsp;&nbsp; {{ $invoice->work_order->job->name}}&nbsp;&nbsp;-&nbsp;&nbsp; {{ $invoice->work_order->job->address_1}} {{ $invoice->work_order->job->address_2}} {{ $invoice->work_order->job->city}} ,{{ $invoice->work_order->job->state}} {{ $invoice->work_order->job->zip}}</td>
                                                </tr>
                                                <tr>
                                                    <?php 
                                                    $firm_name='';
                                                    $name='';
                                                    $address='';
                                                    $email='';
                                                    $customer=$invoice->work_order->job->parties->where('type','customer')->first();
                                                    if (count($customer)>0){
                                                        $customer_contact= $customer->contact;
                                                        if(count($customer_contact)>0){
                                                            $firm_name=$customer->firm->firm_name;
                                                            $name=$customer->firm->fullname;
                                                            $address=$customer_contact->address1.' '.$customer_contact->address2.' '.$customer_contact->city.' , '.$customer_contact->state.' '.$customer_contact->zip;

                                                            $phone=$customer_contact->phone;
                                                            $email=$customer_contact->email;

                                                        };
                                                    }

                                                    ?>
                                                    <td  colspan="4">Job Customer: {{ $firm_name}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $name}}</td>
                                                </tr>
                                                <?php 
                                                $price="";
                                                $quantity="";
                                                $amount=0;
                                                ?>
                                                @foreach ($invoice->lines as $line)
                                                @if (strpos($line->description,'POSTAGE')==true || strpos($line->description,'MAIL')==true || strpos($line->description,'PREVIOUS')==true || strpos($line->description,'CERTIFIED')==true)
                                                <?php 
                                                $price.=' $'.number_format($line->price,2);
                                                $quantity.=' '.$line->quantity;
                                                $amount+=number_format($line->amount,2);
                                                ?>
                                                @else
                                                <tr>
                                                        <td>{{$line->description}}</td>
                                                        <td class="text-center">${{ number_format($line->price,2)}}</td>
                                                        <td class="text-center">{{ $line->quantity }}</td>
                                                        <td class="text-right">${{ number_format($line->amount,2)}}</td>
                                                </tr>
                                                
                                                @endif
                                                @endforeach
                                                @if ($price)
                                                <tr>
                                                        <td>POSTAGE</td>
                                                        <td class="text-center">${{ number_format($amount,2)}}</td>
                                                        <td class="text-center">1</td>
                                                        <td class="text-right">${{ number_format($amount,2)}}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line text-center"><strong>Total</strong></td>
                                                        <td class="thick-line text-right">${{number_format($invoice->total_amount,2)}}</td>
                                                </tr>

                                            @endforeach
                                                <tr>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line text-center"><strong>Batch Invoice Total</strong></td>
                                                        <td class="thick-line text-right"><strong>${{number_format($total_charge,2)}}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                                
                        </div>
                </div>
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/payeezy/js/payeezy_us_v5.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-print').click(function(){
     
    $("#print_contents").show();
    window.print();
});
$(function () {
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('#pay-form').preventDoubleSubmission();
});


 
</script>
    
@endsection