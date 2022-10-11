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
                        <h1 class="page-header">Shared Jobs
                            <div class="pull-right">
                                <a href="#" data-toggle="modal" data-target="#modal-sharejobfromnotice" class="btn btn-primary btn-job-share"><i class="fa fa-share"></i> Share Job from Notice</a>&nbsp;&nbsp;
                            </div>
                        </h1>
                        @component('admin.jobs_shared.sharejobfromnotice')
                        @endcomponent
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'jobs_shared.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="job_name_filter"> Job Name: </label>
                                    {!! Form::text('job_name',session('job_shared_filter.name'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="client_filter">Client: </label>
                                    {!! Form::select('client_filter',$clients,session('job_shared_filter.client'),['class'=>'form-control','id'=>'client_filter'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="shared_client">Monitoring Client: </label>
                                    {!! Form::select('shared_client',$clients,session('job_shared_filter.shared_client'),['class'=>'form-control','id'=>'shared_client'])!!}
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
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Job Name</th>
                                    <th class="text-center">Monitoring Client</th>
                                    <th class="text-center">Client</th>
                                    <th class="text-center">Job Type</th>
                                    <th class="text-center">Date Started</th>
                                    <th class="text-center">Status</th>
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $sjob)
                                <?php
                                    $job = $sjob->job; $user = $sjob->user;
                                ?>
                                @if($job && $user)
                                <tr>
                                    <td> {{ $job->name }}</td>
                                    <td> {{ $user->client ? $user->client->company_name : $user->full_name }}</td>
                                    <td> {{ ($job->client) ? $job->client->company_name : "N/A" }}</td>
                                    <td> {{ title_case($job->type) }}</td>
                                    <td> {{ (strlen($job->started_at) > 0) ? date('m/d/Y', strtotime($job->started_at)): '' }}</td>
                                    <td> {{ $job->status=='notice-of-non-payment' ? 'Demand-For-Payment' : title_case($job->status) }}</td>
                                    <td>
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">    
                                            <li><a href="{{ route('jobs_shared.summary',$job->id)}}"><i class="fa fa-book"></i> View Job Summary</a></li>
                                            <li><a href="{{ route('jobs_shared.unshare',$job->id)}}"><i class="fa fa-times"></i> Unshare</a></li>
                                            @if ($job->linked_to)
                                            <li><a href="{{ route('jobs.edit',$job->linked_to)}}#linked"><i class="fa fa-link"></i> View Linked Job</a></li>
                                            <li><a href="{{ route('jobs_shared.unlink',$job->id)}}"><i class="fa fa-unlink"></i> Unlink</a></li>
                                            @else
                                            <li><a href="#" data-toggle="modal" data-target="#modal-job-link-{{ $job->id }}"><i class="fa fa-link"></i> Link to Job</a></li>
                                            @endif
                                        </ul>
                                        @if (!$job->linked_to)
                                        <div class="modal fade" id="modal-job-link-{{ $job->id }}" tabindex="-1" role="dialog" size="lg">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">Link the shared job [{{$job->name}}] to:</h4>
                                                    </div>
                                                    {!! Form::open(['route' => ['jobs_shared.link_to',$job->id]]) !!}
                                                    <div class="modal-body">
                                                        {!!  Form::select('linked_to',$my_job_list, '', ['class' => 'form-control','required'=>true]) !!}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
                                                        <button class="btn btn-success" type="submit"><i calss="fa fa-times"></i> Link to the job</button>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    </td>
                                </tr>
                                @endif
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