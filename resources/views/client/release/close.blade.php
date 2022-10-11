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
<div class="container">
    {!! Form::open(['route'=>['client.release.close'],'autocomplete' => 'off'])!!}
    {{ Form::hidden('job_id',$job->id)}}
    <div class="col-xs-12 text-center">
        <h3>Would You like to set this job as Closed?</h3>
        <p>
            Doing so will mean no longer being able to enter new notices on this job.
        </p>
    </div>
    <div>&nbsp;</div>
    <div class="col-xs-12 text-center">
        <div class="col-xs-3 col-xs-offset-1 text-Left">
            <button type="submit" class="btn  btn-success btn-block" name="complete" value="yes"><i class="fa fa-check"></i> Yes</button>
            
        </div>
        <div class="col-xs-5">
            &nbsp;
        </div>
        <div class="col-xs-3 text-right">
            <button type="submit" class="btn  btn-danger btn-block" name="complete" value="ni"><i class="fa fa-times"></i> No</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')

<script>


</script>
    
@endsection