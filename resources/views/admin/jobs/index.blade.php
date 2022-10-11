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
                        <h1 class="page-header">Jobs 
                            <a class="btn btn-success pull-right" href="{{ route('jobs.create')}}"><i class="fa fa-plus"></i> Add Job</a>
                        </h1>
                       
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'jobs.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="job_name_filter"> Job Name: </label>
                                    {!! Form::text('job_name',session('job_filter.name'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                <label for="client_filter">Client: </label>
                                {!! Form::select('client_filter',$clients,session('job_filter.client'),['class'=>'form-control','id'=>'client_filter'])!!}
                              </div>
                                <div class="form-group">
                                    <label for="job_type_filter"> Job Type: </label>
                                    {!! Form::select('job_type',['all' => 'All','public'=>'Public','private'=>'Private'],session('job_filter.job_type'),['class'=>'form-control'])!!}
                                </div>
                                
                                <div class="form-group">
                                    <label for="job_type_filter"> Date Started: </label>
                                    {!! Form::text('daterange',session('job_filter.daterange'),['class'=>'form-control'])!!}
                                </div>
                                 <div class="form-group">
                                    <label for="job_type_filter"> Job Status: </label>
                                    {!! Form::select('job_status',$job_statuses,session('job_filter.job_status'),['class'=>'form-control'])!!}
                                </div>

                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('jobs.resetfilter') }}" class="btn btn-danger">Clear</a>
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
                                    <th class="text-center">Client</th>
                                    <th class="text-center">Job Type</th>
                                    <th class="text-center">Date Started</th>
                                    <th class="text-center">Status</th>
                                    <!-- <th class="text-center">Job Parites</th> -->
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $job)
                                <tr>
                                    <td> {{ $job->name }}</td>
                                    <td> {{ ($job->client) ? $job->client->company_name : "N/A" }}</td>
                                    <td> {{ title_case($job->type) }}</td>
                                    <td> {{ (strlen($job->started_at) > 0) ? date('m/d/Y', strtotime($job->started_at)): '' }}</td>
                                    <td> {{ $job->status=='notice-of-non-payment' ? 'Demand-For-Payment' : title_case($job->status) }}</td>
                                    <!-- <td class="text-center"> {{ $job->parties->count() }}</td> -->
                                    <td>
                                    <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                    </button>
                                        @component('admin.jobs.components.deletemodal')
                                        @slot('id') 
                                            {{ $job->id }}
                                        @endslot
                                        @slot('client_name') 
                                            {{ $job->name }}
                                        @endslot
                                        @endcomponent
                                        
                                        <li><a href="{{ route('jobs.edit',$job->id) ."?page" . $jobs->currentPage()}}"><i class="fa fa-pencil"></i> Edit</a></li>
                                        <li><a href="{{ route('parties.index',$job->id) . "?page" . $jobs->currentPage()}}"><i class="fa fa-users"></i> Parties Assignment</a></li>
                                        @if($job->status != 'closed')
                                        <li><a href="{{ route('workorders.create',['job_id' =>$job->id])}}"><i class="fa fa-briefcase"></i> Create Work Order</a></li>
                                        @endif
                                        <li><a href="{{ route('workorders.setfilter') . '?resetfilter=true&job_filter=' . $job->id }}"><i class="fa fa-eye"></i> View Work Orders</a></li>
                                        <li><a href="{{ route('jobs.summary',$job->id)}}"><i class="fa fa-book"></i> View Job Summary</a></li>
                                        @if($job->status != 'closed')
                                        <li role="separator" class="divider"></li>
                                        @component('admin.jobs.components.closemodal')
                                        @slot('id') 
                                            {{ $job->id }}
                                        @endslot
                                        @slot('client_name') 
                                            {{ $job->name }}
                                        @endslot
                                        @endcomponent
                                        @else
                                            </ul>
                                        @endif
                                        
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