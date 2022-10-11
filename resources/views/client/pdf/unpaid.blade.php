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
        <div class="row">
            <div  class="col-xs-12">
                <h3 class="page-header">
                    Purchased Postage - Payment Decline
                </h3>       
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if (count($errors) > 0)
        <div class ="row">
            <div class ="col-xs-12 message-box">
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
        <div class ="row">
            <div class ="col-xs-12">
                <h5>Your credit card has been declined. </h5>
                <p>Please check the information submitted
                and try again.  If error continues,
                contact your credit card company or 
                add a new credit card by going into
                the Settings Menu and then into Invoices
                Menu and fill out requested information.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <a href="{{route('client.notices.edit', $work_id)}}" class="btn btn-success pull-right">&nbsp;&nbsp;&nbsp;&nbsp;Goto ToDo tab of Work Order Edit page&nbsp;&nbsp;&nbsp;&nbsp;</a>
            </div>
        </div>
    </div>
</div>
   
@endsection

@section('scripts')

<script>

</script>
    
@endsection