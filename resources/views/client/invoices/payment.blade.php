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
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">Invoice{{count($invoices)>1 ? 's' :''}} Payment 
                        
                    <div class="pull-right">

                    </div>
                       
                </h1> 
                    
                </h1>       
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
                    <div class="col-md-12">
                            <div class="panel panel-default">
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
                                                    <td  colspan="4">Invoice ID: {{$invoice->id}} - Job Name: {{ $invoice->work_order->job->name}}</td>
                                                        
                                                </tr>
                                                @foreach ($invoice->lines as $line)
                                                <tr>
                                                        <td>{{$line->description}}</td>
                                                        <td class="text-center">${{ number_format($line->price,2)}}</td>
                                                        <td class="text-center">{{ $line->quantity }}</td>
                                                        <td class="text-right">${{ number_format($line->amount,2)}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line text-center"><strong>Total</strong></td>
                                                        <td class="thick-line text-right">${{number_format($invoice->total_amount,2)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                                
                        </div>
                </div>
                
                <div class ="col-xs-4">
                     <h4><i class="fa fa-lock"></i> Payment Processing</h4>

                        <p>We will process your payment using a token retrieved from our payment gateway. Credit Card information is never stored on our servers, we use Payeezy for payment processing.</p>
                        <img  class="img-responsive col-xs-12 " src="{{ asset('/images/payeezylogo.png') }}" alt=""/>
                    </div> 
                <div class="col-xs-8">
                    {!! Form::open(['route' => 'client.invoices.submitpayment', 'id' => 'pay-form'])!!}
                        
                    {{ Form::hidden('invoices_id', $invoices_id) }}
                   
                        {{ Form::hidden('todownload', $todownload) }}
                        {{ Form::hidden('work_id', $work_id) }}
                        {{ Form::hidden('attach_id', $attach_id) }}
                       
                   
                      
                      
                   
                    <div class="col-xs-4 pull-right">
                            <button type="submit" class="btn btn-success btn-pay btn-block form-control"> <i class="fa fa-money"></i> &nbsp; Pay ${{number_format($total_charge,2)}}
                             @if($todownload) & Downoad @endif</button>
                       
                    </div>

                    </form>
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
$('.btn-pay').click(function(){
    $('.btn-pay').addClass("disabled");
    $('.btn-pay').css('pointer-events','none');
});
$(function () {
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();
    $('#pay-form').preventDoubleSubmission();

    <!-- Building JSON resquest and submitting request to Payeezy sever -->

      
});


 
</script>
    
@endsection