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

.stepwizard-step p {
    margin-top: 0px;
    color:#666;
}
.stepwizard-row {
    display: table-row;
}
.stepwizard {
    display: table;
    width: 100%;
    position: relative;
    pointer-events:none;
}

.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
}
.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}
.btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
}
@media screen and (max-width: 500px) {
    .stepwizard-step p {
        display: none !important;
    }
}
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
<div id="top-wrapper" >
    <br>
    <div class="stepwizard">
      <div class="stepwizard-row setup-panel">
          <div class="stepwizard-step col-xs-3">
              <a type="button" class="btn  btn-default btn-circle">1</a>
              <p><small>Job/Contract Information & Workorder</small></p>
          </div>
          <div class="stepwizard-step col-xs-3"> 
              <a type="button" class="btn btn-default btn-circle">2</a>
              <p><small>Job/Contract Parties & Attachments</small></p>
          </div>
          <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >3</a>
              <p><small>Payment</small></p>
          </div>
          <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-success btn-circle" >4</a>
              <p style="color: black"><small><strong>Confirmation</strong></small></p>
          </div>
      </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h3 class="page-header">
                    Work Order Created
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
                <p>Your initial payment has been processed successfully
                @if($notice->type=='notice-to-owner'), 
                this included your Work Order to owner fee, 1 certified 
                mail fee and 1 regular mail fee. <strong>We will bill for any 
                additional mail pieces after Work Order is completed.</strong>  
                @else
                . 
                @endif
                You will be notified when your Work Order is ready. 
                You can review your order Status on My Work Orders menu..
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
                            <div class="col-xs-12 col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><strong>Work Order Info</strong></h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table">
                                            <tr class="no-line">
                                                <td> Work Order Type:</td>
                                                <td>{{ $wo_types[$notice->type] }}</td>
                                            </tr>
                                            <tr>
                                                <td> Work Order Number:</td>
                                                <td>{{ $notice->number}}</td>
                                            </tr>
                                            <tr>
                                                <td> Work Order Created Date:</td>
                                                <td>{{ $notice->created_at->format('m-d-Y')}}</td>
                                            </tr>
                                            <tr>
                                                <td> Work Order Due Date:</td>
                                                <td>{{ $notice->due_at->format('m-d-Y')}}</td>
                                            </tr>
                                            <tr>
                                                <td>Job Number:</td>
                                                <td>{{ $job->number}}</td>
                                            </tr>
                                            <tr>
                                                <td>Job/Contract Name:</td>
                                                <td>{{ $job->name}}</td>
                                            </tr>
                                            <tr>
                                                <td>Job/Contract Address:</td>
                                                <td> {!! $job->full_address_no_country !!}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    Mailing Contacts
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="row"><div class="row">
                                                    @foreach($contacts as $party)
                                                        <div class="col-xs-12 col-md-6">
                                                            <div class="well well-sm">
                                                                {{ $parties_type[$party->type] }}<br>
                                                                @if($party->contact->entity->firm_name)
                                                                    <label>{{ $party->contact->entity->firm_name }}</label> <br>
                                                                    @if($party->contact->full_name)
                                                                        <span> <small><i class="fa fa-user fa-fw"></i> {{ $party->contact->full_name }}</small></span> <br>
                                                                    @endif
                                                                @else
                                                                   <i class="fa fa-user fa-fw"></i> {{ $party->contact->full_name }}<br />
                                                                @endif
                                                               
                                                                {!! $party->contact->address_no_country !!}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </div></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-md-6">
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
                            <div class="col-md-12">
                                <center>There may be additional costs for postage at work order completion. </center>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <a href="{{route('client.notices.index')}}" class="btn btn-success pull-right">&nbsp;&nbsp;&nbsp;&nbsp;Goto Work Orders List&nbsp;&nbsp;&nbsp;&nbsp;</a>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-xs-12">
                                <a href="{{route('wizard2.getjobworkorder')}}" class="btn btn-success pull-right">Create Another Work Order</a>
                            </div>
                        </div>
                        
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