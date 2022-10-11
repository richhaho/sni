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
                  <div class="stepwizard-step col-xs-4">
                      <a type="button" class="btn  btn-default btn-circle">1</a>
                      <p><small>Job/Contract Information & Workorder</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-4"> 
                      <a type="button" class="btn btn-default btn-circle">2</a>
                      <p><small>Job/Contract Parties & Attachments</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-4">    <a type="button" class="btn btn-success btn-circle" >3</a>
                      <p style="color: black"><small><strong>Choice Next</strong></small></p>
                  </div>
              </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h3 class="page-header">What do you want to do next?</h3>
                    </div>
                </div>
            </div>

            @if (Session::has('message'))
                <div class="col-xs-12 message-box">
                  <div class="alert alert-info">{{ Session::get('message') }}</div>
                </div>
            @endif
                        
            <div class="container-fluid">
                <div class="row" style="height: 500px">
                    <div class="col-xs-12 col-md-4">
                        <br>
                        <a class="btn btn-success form-control" href="{{ route('client.research.start',[$job_id])}}"> Start Research</a>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <br>  
                        <a class="btn btn-success form-control" href="{{ route('client.notices.document',$workorder_id)}}"> Generate Document(s)</a>
                    </div>
                    <div class="col-xs-12 col-md-4">
                    <br>
                        <a class="btn btn-success form-control" href="{{ route('wizard2.getjobworkorder')}}"> Input Another Work Order </a>
                    </div>
                </div>
            </div>
        </div>
   
@endsection

@section('scripts')
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
}) 
</script>
@endsection
