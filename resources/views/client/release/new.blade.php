@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => 'client.release.pdf','autocomplete' => 'off']) !!}
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Release 
                    <div class="pull-right">
                        <button class="btn btn-success next-button" type="submit">  <span>  Next </span> <i class="fa fa-chevron-right"></i></button>
                        
                    </div>
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            @if (Session::has('message'))
                <div class="col-xs-12 message-box">
                <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                </div>
            @endif
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
                                Select "Job"  and choose type of release
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Job Name:</label>
                                    {!!  Form::select('job_id',$jobs,old("job_id",''), ['class' => 'form-control','id'=>'job_id']) !!}
                                </div>
                                </div>
                                
                                <div class="row">
                                    
                                <div class="col-xs-12 form-group">
                                    <label>Release Type:</label>
                                    {!!  Form::select('release_type',$releases,old("release_type",''), ['class' => 'form-control','id'=>'release_type']) !!}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 parties-list">
                        
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

$(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
 });
  $('[data-toggle="tooltip"]').tooltip()
  $('#job_id').select2();

 
    $('#job_id').on ('change', function () {
       var xid = $(this).val();
       $('.parties-list').load('{{ url ("/client/release/job")}}' + '/' + xid + '/parties', function() {

       });
       
    });

   $('#job_id').trigger('change');

});


</script>
    
@endsection