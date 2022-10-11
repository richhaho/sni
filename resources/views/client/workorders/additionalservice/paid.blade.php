@extends('client.layouts.app')

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
  .table{
    word-break: break-word;
  }

</style>
@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
<div id="top-wrapper" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h3 class="page-header">
                    Additional Service Purchased
                </h3>       
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if (count($errors) > 0)
        <div class ="row">
            <div class="col-xs-12 message-box">
                <div class="alert alert-danger">            
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class ="col-xs-12">
                <p>
                    Your initial payment has been processed successfully.
                    You can view your todos on the "Todo" tab of the Work Order edit page.
                </p>
            </div>
            <div class ="col-xs-12">
                <p class="text-danger"><strong>Any outstanding invoices at the end of the week will be debited against the credit card on file.</strong></p>
                <h4>Here is your transaction  summary </h4>
            </div>
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"> Amount Charged: ${{number_format($payment->amount,2)}}</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><strong>Invoice Info</strong></h3>
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

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <a href="{{route('client.notices.edit', $work_id)}}?#todos" class="btn btn-success pull-right">&nbsp;&nbsp;&nbsp;&nbsp;Goto Todos tab of Work Order Edit page&nbsp;&nbsp;&nbsp;&nbsp;</a>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-xs-12">
                                <a class="btn btn-warning pull-right" href="{{ route('client.notices.requestService',$work_id)}}"> Request Additional Services</a>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   
@endsection

@section('scripts')

<script>

</script>
    
@endsection