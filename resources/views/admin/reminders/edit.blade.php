@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['reminders.update',$reminder->id], 'method'=> 'POST','autocomplete' => 'off']) !!}     
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Reminder
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('reminders.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
                    </div>
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
                @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            </div>
                @endif
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Reminder Details
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                  <div class="col-xs-12 col-md-12 form-group">
                                      <label>Reminder Name:</label>
                                      <input id="reminder_name" type="text" value="{{$reminder->reminder_name}}" class="form-control noucase" name="reminder_name" required autofocus>
                                  </div>
                                  <div class="col-md-12 form-group">
                                      <label>Email Subject:</label>
                                      {!!  Form::text('email_subject',$reminder->email_subject, ['class' => 'form-control']) !!}
                                  </div>
                                  <div class="col-md-12 form-group">
                                      <label>Email Message:</label>
                                      {!!  Form::textarea('email_message',$reminder->email_message, ['class' => 'form-control','rows'=>5]) !!}
                                  </div>
                                  <div class="col-md-12 form-group">
                                      <label>SMS/Text Message:</label>
                                      {!!  Form::textarea('sms_message',$reminder->sms_message, ['class' => 'form-control','rows'=>5,'maxlength'=>1500]) !!}
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-xs-12 col-md-4 form-group">
                                        <label>First Send Date/Time:</label>
                                        <input name="first_send_date"  value="{{ old("first_send_date", (strlen($reminder->first_send_date) > 0) ? date('m/d/Y h:i A', strtotime($reminder->first_send_date)): '')}}"  data-date-autoclose="true" class="form-control date-picker" data-toggle="tooltip" data-placement="top" title="" required>
                                  </div>
                                  <div class="col-xs-12 col-md-4 form-group">
                                        <label>Run Every:</label>
                                        <input name="send_frequency" value="{{$reminder->send_frequency}}" class="form-control" type="number" min="1" required>
                                  </div>
                                  <div class="col-xs-12 col-md-4 form-group">
                                        <label>Period:</label>
                                        {!!  Form::select('period',$period,old('period',$reminder->period), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'']) !!}
                                  </div>
                                  
                                </div>

                                <div class="row">
                                  <div class="col-xs-12 col-md-12 form-group">
                                    <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="status" type="checkbox" {{ ($reminder->status==1) ? 'checked' :''}}><span>Disable/Enable</span>
                                        </label>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
    {!! Form::close() !!}
@endsection
@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/moment/js/moment.min.js') }}" type="text/javascript"></script> 
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

<script>
var items_id=1;
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});
$('input').keydown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});

$(function () {
   $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
  
});
$('.date-picker').datetimepicker();  
</script>
    
@endsection
