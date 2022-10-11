@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => 'workorders.store','autocomplete' => 'off']) !!}
        {!! Form::hidden('status','open')!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Work Order
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('workorders.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Details
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::select('job_id',$jobs_list,old("job_id",$job_id), ['class' => 'form-control','id'=>'job_id']) !!}
                                    </div>
                                </div>
                                 <div class="row">
                                   
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Work Order Type:</label>
                                        {!!  Form::select('type',$wo_types,old("type"), ['class' => 'form-control']) !!}
                                    </div>
                                    
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Due Date:</label>
                                        <input name="due_at"  value="{{ old("due_at")}}" class="form-control date-picker" data-date-autoclose="true"  data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                     
                                
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="is_rush" type="checkbox"><span>Is Rush?</span>
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
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
 
  $('.date-picker').datepicker();
 
});
</script>
    
@endsection