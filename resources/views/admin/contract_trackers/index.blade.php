@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/css/sidebar.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
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
                        <h1 class="page-header">Contract Tracker
                        </h1>
                    </div>
                        <div class="col-xs-12" id="filters-form">
                            {!! Form::open(['route' => 'contract_trackers.setfilter', 'class'=>'form-inline'])!!}
                                <div class="form-group">
                                    <label for="job_name_filter"> Contract Name: </label>
                                    {!! Form::text('name',session('contract_trackers_filter.name'),['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    <label for="client_filter">Client: </label>
                                    {!! Form::select('client_id',$clients,session('contract_trackers_filter.client_id'),['class'=>'form-control','id'=>'client_id'])!!}
                                </div>

                            <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                             <a href="{{ route('contract_trackers.resetfilter') }}" class="btn btn-danger">Clear</a>
                            {!! Form::close() !!}
                           
                        </div>
                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        <div class="col-xs-12" style="margin-bottom: 10px">
                            <button data-toggle="modal" data-target="#modal-contract-tracker-create" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New Contract Tracker</button>
                            @component('admin.contract_trackers.components.createmodal')
                            @endcomponent
                        </div>
                        @if(count($contract_trackers) > 0 )
                        <div class="col-xs-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Name</th>
                                        <th>Start Date</th>
                                        <th>Contract</th>
                                        <th>Converted to Job</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract_trackers as $tracker)
                                    <tr>
                                        <td> {{$tracker->client->company_name}}</td>
                                        <td style="word-break: break-all;max-width: 200px;"> {{$tracker->name}}</td>
                                        <td> {{date('m/d/Y', strtotime($tracker->start_date))}}</td>
                                        <td> 
                                            @if($tracker->contract_file)
                                            <a href="{{route('contract_trackers.download', $tracker->id)}}"><i class="fa fa-download"></i> Download</a>
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                                $job = $tracker->job();
                                            ?>
                                            @if($job)
                                            <a href="{{route('jobs.edit', $job->id)}}">View Job</a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group pull-right dropup">
                                                <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @if(!$tracker->is_converted)
                                                    <li><a href="{{route('jobs.create')}}?contract_tracker={{$tracker->id}}"><i class="fa fa-exchange"></i> Convert To Job and Notice</a></li>
                                                    @endif
                                                    <li><a href="#" data-toggle="modal" data-target="#modal-contract-tracker-edit-{{$tracker->id}}"><i class="fa fa-edit"></i> Edit</a></li>
                                                    <li><a href="#" data-toggle="modal" data-target="#modal-contract-tracker-delete-{{$tracker->id}}"><i class="fa fa-times"></i> Delete</a></li>
                                                </ul>
                                                @component('admin.contract_trackers.components.editmodal')
                                                    @slot('id') 
                                                        {{ $tracker->id }}
                                                    @endslot
                                                    @slot('client_id') 
                                                        {{ $tracker->client_id }}
                                                    @endslot
                                                    @slot('name') 
                                                        {{ $tracker->name }}
                                                    @endslot
                                                    @slot('start_date') 
                                                        {{ $tracker->start_date }}
                                                    @endslot
                                                    @slot('contract_file') 
                                                        {{ $tracker->contract_file }}
                                                    @endslot
                                                @endcomponent
                                                @component('admin.contract_trackers.components.deletemodal')
                                                    @slot('id') 
                                                        {{ $tracker->id }}
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
                            {{ $contract_trackers->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Contract trackers found</h5>
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
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
    $.fn.select2.defaults.set("theme", "bootstrap");    
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('#client_id').select2();
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $('.date-picker').datepicker();
    $(":file").filestyle();
});
</script>
@endsection