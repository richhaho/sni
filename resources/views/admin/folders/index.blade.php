@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    td .btn{
        margin-top: 5px;
    }
</style>
@endsection


@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <h1 class="page-header">Reporting
                    @component('admin.folders.components.createmodal')
                    @endcomponent
                    </h1>
                </div>
                <div class="col-xs-12"><br></div>
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                        <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                    </div>
                @endif
                @if(count($folders) > 0 )    
                <div class="col-xs-12">
                @foreach($folders as $folder)
                    <div class="panel panel-default folder-{{$folder->id}}">
                        <div class="panel-heading" role="tab" id="heading{{$folder->id}}">
                            <div class="panel-title ">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$folder->id}}" aria-expanded="true" aria-controls="collapse{{$folder->id}}">
                                    <h4>
                                        <i class="fa fa-folder"></i> {{$folder->name}}
                                        @component('admin.folders.components.reports.createmodal')
                                        @slot('folder_id')
                                            {{ $folder->id }}
                                        @endslot
                                        @slot('folder_name')
                                            {{ $folder->name }}
                                        @endslot
                                        @endcomponent
                                        @component('admin.folders.components.editmodal')
                                        @slot('id')
                                            {{ $folder->id }}
                                        @endslot
                                        @slot('name')
                                            {{ $folder->name }}
                                        @endslot
                                        @endcomponent
                                        @component('admin.folders.components.deletemodal')
                                        @slot('id')
                                            {{ $folder->id }}
                                        @endslot
                                        @endcomponent
                                    </h4>
                                </a>
                            </div>
                            <div id="collapse{{$folder->id}}" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="heading{{$folder->id}}">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Report Name</th>
                                                    <th>Client Type</th>
                                                    <th>Query</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($folder->reports() as $report)
                                                <tr>
                                                    <td>{{$report->name}}</td>
                                                    <td>{{strtoupper($report->client_type)}} Service</td>
                                                    <td>{{$report->sql}}</td>
                                                    <td>
                                                        @component('admin.folders.components.reports.deletemodal')
                                                            @slot('id') 
                                                                {{ $report->id }}
                                                            @endslot

                                                        @endcomponent
                                                        @component('admin.folders.components.reports.editmodal')
                                                            @slot('folder_id')
                                                                {{ $folder->id }}
                                                            @endslot
                                                            @slot('folder_name')
                                                                {{ $folder->name }}
                                                            @endslot
                                                            @slot('id') 
                                                                {{ $report->id }}
                                                            @endslot
                                                            @slot('name') 
                                                                {{ $report->name }}
                                                            @endslot
                                                            @slot('client_type') 
                                                                {{ $report->client_type }}
                                                            @endslot
                                                            @slot('sql') 
                                                                {{ $report->sql }}
                                                            @endslot
                                                        @endcomponent
                                                        @component('admin.folders.components.reports.runmodal')
                                                            @slot('folder_id')
                                                                {{ $folder->id }}
                                                            @endslot
                                                            @slot('folder_name')
                                                                {{ $folder->name }}
                                                            @endslot
                                                            @slot('id') 
                                                                {{ $report->id }}
                                                            @endslot
                                                            @slot('name') 
                                                                {{ $report->name }}
                                                            @endslot
                                                            @slot('client_type') 
                                                                {{ $report->client_type }}
                                                            @endslot
                                                            @slot('sql') 
                                                                {{ $report->sql }}
                                                            @endslot
                                                        @endcomponent
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="col-xs-12 text-center">
                    {{ $folders->links() }}
                </div>
                @else
                <div class="col-xs-12">
                    <h5>No Folder found</h5>
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
 
 
<script>
    
    
$(function () {
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });

    var anchor = window.location.hash;
    if (anchor.length >0 ) {
        $(".collapse").collapse('hide');
        $(anchor).collapse('show'); 
    }
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find("i.fa-folder").removeClass("fa-folder").addClass("fa-folder-open");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".fa-folder-open").removeClass("fa-folder-open").addClass("fa-folder");
    });
    
});
</script>
@endsection