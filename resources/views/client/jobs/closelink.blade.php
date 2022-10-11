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
<div id="top-wrapper" >
    <div class="container-fluid">
    <div class="col-xs-12">
        <h2 class="page-header">
            Are you sure to close the job: {{$job->name}}?
        </h2>
    </div>
    <div class="col-xs-12">
        {!! Form::open(['route' => ['client.jobs.close',$job->id]]) !!}
        {!! Form::hidden('redirect_to', 'jobs') !!}
        <div class="col-xs-12">
            <br>
            <button class="btn btn-success" type="submit"><i class="fa fa-times"></i> Close the job</button>
            <a class="btn btn-danger " href="{{route('client.jobs.edit',$job->id)}}"><i class="fa fa-chevron-left"></i> Back</a> &nbsp;&nbsp;
            <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('scripts')
<script>
</script>
    
@endsection