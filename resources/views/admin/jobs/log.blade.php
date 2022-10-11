@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
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
                        <h1 class="page-header">Job Log 
                            <a class="btn btn-warning pull-right" href="{{ route('jobs.edit', $job->id)}}"><i class="fa fa-arrow-left"></i> Back</a>
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => ['joblogs.setfilter', $job->id], 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="job_name_filter"> User: </label>
                                    {!! Form::text('fullname',session('joblog_filter.fullname'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_type_filter"> Edited Date: </label>
                                    {!! Form::text('daterange',session('joblog_filter.daterange'),['class'=>'form-control'])!!}
                                </div>

                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('joblogs.resetfilter', $job->id) }}" class="btn btn-danger">Clear</a>
                            {!! Form::close() !!}
                           
                        </div>
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($logs) > 0 )
                        <div class="col-xs-12">
                            <h3>Job Name: {{ $job->name }}</h3>
                        </div>
                        <div class="col-xs-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">User Name</th>
                                        <th class="text-center">Edited Time</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Type</th>
                                        <th class="col-xs-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td> {{ $log->user_name }}</td>
                                        <td> {{ date('m/d/Y H:i:s', strtotime($log->edited_at)) }}</td>
                                        <td>
                                            @foreach(json_decode($log->data) as $record)
                                            <li>Field: <strong>{{strtoupper(str_replace('_', ' ', $record->field))}}</strong>, Previous Value: <strong>{{$record->old}}</strong>, Updated Value: <strong>{{$record->new}}</strong></li>
                                            @endforeach
                                        </td>
                                        <td> {{ strtoupper($log->type) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @component('admin.jobs.components.deletelogmodal')
                                                @slot('id') 
                                                    {{ $log->id }}
                                                @endslot
                                                @slot('job_id') 
                                                    {{ $job->id }}
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
                            {{ $logs->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Job Logs found</h5>
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
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
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