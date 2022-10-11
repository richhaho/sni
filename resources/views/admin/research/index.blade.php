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
                        <h1 class="page-header">Research Queue</h1>
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'research.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="work_rush"> Has Corners: </label>
                                    {!! Form::select('has_corners',['all' => 'All','0'=>'No','1'=>'Yes'],session('research_filter.has_corners'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="job_name_filter"> County: </label>
                                    {!! Form::text('county',session('research_filter.county'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="work_rush"> Rush Type: </label>
                                    {!! Form::select('work_rush',['all' => 'All','0'=>'No','1'=>'Yes'],session('research_filter.work_rush'),['class'=>'form-control'])!!}
                                </div>
                                <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                                <a href="{{ route('research.resetfilter') }}" class="btn btn-danger">Clear</a>
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
                                    <th class="text-center">Work Order Number</th>
                                    <th class="text-center">Job Name</th>
                                    <th class="text-center">Job Address</th>
                                    <th class="text-center">Has Corners</th>
                                    <th class="text-center">Job County</th>
                                    <th class="text-center">Due Date</th>
                                    <th class="text-center">Rush</th>
                                    
                                    <th class="col-xs-1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $job)
                                <tr>
                                    <td> {{ $job->firstWorkorder()->number}}</td>
                                    <td> {{ $job->name }}</td>
                                    <td> {{ $job->address_1 }} {{ $job->address_2 }}</td>
                                    <td> {{ $job->address_corner ? 'Yes' : 'No' }}</td>
                                    <td> {{ $job->county }}</td>
                                    <td> {{ $job->firstWorkorder()->due_at->format('Y-m-d') }}</td>
                                    <td> {{ $job->firstWorkorder()->is_rush ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('research.start',$job->id)}}"><i class="fa fa-book"></i> Start Research</a></li>
                                            </ul>
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
                            <h5>No Workorders found</h5>
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
});
</script>
@endsection