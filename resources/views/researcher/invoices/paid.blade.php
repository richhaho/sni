@extends('researcher.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .table > tbody > tr > .no-line {
      border-top: none;
  }

  tr.no-line > td {
      border-top: none!important;
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
    @include('researcher.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">
                    <h1 class="page-header">Payment Successful
                    
                       
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
                    <p>Your Payment has been processed successfully 
                         @if($todownload)
                                   , you can download the required file by clicking  the following button <a href="{{ route('client.notices.downloadattachment',[$work_id,$attach_id])}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Download</a>
                         @endif
                        </p>
                        <h4>Here is your transaction's  summary </h4>
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title"> Amount Charged: ${{number_format($total_charge,2)}}</h1>
                                </div>
                                <div class="panel-body">
                                    <div class="row">

                                        <div class="col-md-12">
                                                    <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Selected Invoices summary</strong></h3>
                                </div>
                                
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                            <tr>
                                                <td><strong>Item</strong></td>
                                                <td class="text-center"><strong>Price</strong></td>
                                                <td class="text-center"><strong>Quantity</strong></td>
                                                <td class="text-right"><strong>Totals</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($invoices as $invoice)    
                                            <tr> <td class="thick-line" colspan="4">&nbsp;</td></tr>
                                                <tr>
                                                    <td  colspan="4">Invoice ID: {{$invoice->id}} Job Name: {{ $invoice->work_order->job->name}}</td>
                                                        
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
                                        <div class="col-md-12 text-right">
                                            <a href="{{route('invoices.index')}}" class="btn btn-success"><i class="fa fa-bar-chart"></i> Back to Invoices</a>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                </div>
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
   
@endsection

@section('scripts')

<script>

</script>
    
@endsection