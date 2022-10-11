@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('css')
<link href="{{asset('vendor/bootstrap-datarange/css/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
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
                    <h1 class="page-header">Reporting</h1>
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
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($folder->reports() as $report)
                                                @if ($report->client_type == 'both' || $client_type == $report->client_type)
                                                <tr>
                                                    <td>{{$report->name}}</td>
                                                    <td>{{strtoupper($report->client_type)}} Service</td>
                                                    <td>
                                                        @component('client.folders.components.reports.runmodal')
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
                                                        @component('client.folders.components.reports.subscribemodal')
                                                            @slot('id') 
                                                                {{ $report->id }}
                                                            @endslot
                                                            @slot('name') 
                                                                {{ $report->name }}
                                                            @endslot
                                                        @endcomponent
                                                    </td>
                                                </tr>
                                                @endif
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
<script src="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<script>
$(function () {
    $('.multi-select-email').multiselect({
        includeSelectAllOption: true,
    });
    $('.multi-select-weekdays').multiselect({
        includeSelectAllOption: true,
    });
    $('.multi-select-email').change(function() {
        const el = $(this).parent().parent();
        el.find('.email-content').val($(this).val().join(','))
        el.find('.emails-label').text($(this).val().join(',  '))
        if ($(this).val().length>0) {
            el.find('.users-email-required').addClass('hidden');
        } else {
            el.find('.users-email-required').removeClass('hidden');
        }
    });
    $('.multi-select-weekdays').change(function() {
        const el = $(this).parent().parent();
        el.find('.weekdays-content').val($(this).val().join(','))
        el.find('.weekdays-label').text($(this).val().join(',  '))
        if ($(this).val().length>0) {
            el.find('.weekdays-required').addClass('hidden');
        } else {
            el.find('.weekdays-required').removeClass('hidden');
        }
    });

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