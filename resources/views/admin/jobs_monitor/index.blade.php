@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
    
    input[name="daterange"] {
            min-width: 180px;
    }
</style>
@endsection

@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">Monitor Job</h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'jobs_shared.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="job_name_filter"> Job Name: </label>
                                    {!! Form::text('job_name',session('job_shared_filter.name'),['class'=>'form-control'])!!}
                                </div>
                                
                                <div class="form-group">
                                    <label for="job_type_filter"> Job Type: </label>
                                    {!! Form::select('job_type',['all' => 'All','public'=>'Public','private'=>'Private'],session('job_shared_filter.job_type'),['class'=>'form-control'])!!}
                                </div>
                                 
                                <div class="form-group">
                                    <label for="job_type_filter"> Date Started: </label>
                                    {!! Form::text('daterange',session('job_shared_filter.daterange'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_type_filter"> Job Status: </label>
                                    {!! Form::select('job_status',$job_statuses,session('job_shared_filter.job_status'),['class'=>'form-control'])!!}
                                </div>

                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('jobs_shared.resetfilter') }}" class="btn btn-danger">Clear</a>
                            {!! Form::close() !!}
                           
                        </div>
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($jobs) > 0 )
                        <div class="col-xs-12" style="overflow-x: scroll;padding-bottom:160px">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Job Name</th>
                                    <th class="text-center">Job Type</th>
                                    <th class="text-center">Date Started</th>
                                    <th class="text-center">Status</th>
                                    <!-- <th class="text-center">Job Parties</th> -->
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($jobs as $job)
                               
                                <tr>
                                    <td> {{ $job->name }}</td>
                                    <td> {{ title_case($job->type) }}</td>
                                    <td> {{ (strlen($job->started_at) > 0) ? date('m/d/Y', strtotime($job->started_at)): '' }}</td>
                                    <td> {{ $job->status=='notice-of-non-payment' ? 'Demand-For-Payment' : title_case($job->status) }}</td>
                                    <td>
                                        <div class="btn-group pull-right">
                                        <a href="#" data-toggle="modal" data-target="#modal-qrcode-{{ $job->id }}" class="btn btn-primary"><i class="fa fa-qrcode"></i> Scan QR Code</a>
                                        @component('admin.jobs_monitor.qrcodemodal')
                                        @slot('job_id') 
                                            {{ $job->id }}
                                        @endslot
                                        @slot('user_id') 
                                            {{ Auth::user()->id }}
                                        @endslot
                                        @endcomponent
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $jobs->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Jobs found</h5>
                        </div>
                        @endif
                    
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{asset('vendor/bootstrap-datarange/js/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script>
    $.fn.select2.defaults.set("theme", "bootstrap");    
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('#client_filter').select2();
    var start = moment().subtract(29, 'days');
    var end = moment();
    
    function cb(start, end) {
        if ( $('input[name="daterange"] span').html() =='') {
            $('input[name="daterange"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    }
    
    $('input[name="daterange"]').daterangepicker({
        timePicker: false,
        autoUpdateInput: false,
        locale: {
            format: 'MM-DD-YYYY'
        },  
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
        
    }, function(start, end, label) {
            $('input[name="daterange"]').val(start.format('MM-DD-YYYY') + ' - ' + end.format('MM-DD-YYYY'));
       });
    cb(start, end);
});
</script>
@endsection