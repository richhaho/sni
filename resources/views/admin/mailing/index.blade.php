@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection
@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style>
    h1.with-buttons {
        display: block;
        width: 100%;
        float: left;
    }
    .page-header h1 { margin-top: 0; }
    
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
                    <div class="page-header col-xs-12">
                        <div class="col-xs-12">
                            <h1 class="" > Mailing Batches</h1>
                            <div class="text-right">
                        
                                <a class="btn btn-success " target="_blank" href="{{route('mailing.create')}}" id="add-batch"><i class="fa fa-plus"></i> New Batch</a>
                                <a class="btn btn-primary" onclick="location.reload(true);"><i class="fa fa-refresh"></i> Reload</a>
                    
                        </div>
                        </div>
                        
                        
                    </div>
                       
                    
                         @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                            </div>
                        @endif
                    
                        
                        @if(count($batches) > 0 )
                        <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date Created</th>
                                    <th>Number of Documents</th>
                                    <th>Manifest</th>
                                    <th class="col-xs-4">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batches as $batch)
                                <tr>
                                    <td> {{ $batch->id  }}</td>
                                    <td> {{ $batch->created_at  }}</td>
                                    <td> {{ count($batch->details)}}</td>
                                    <td>
                                    @if(!$batch->manifest_file)
                                        @component('admin.mailing.components.uploadmodal')
                                        @slot('id') 
                                            {{ $batch->id }}
                                        @endslot
                                        @endcomponent
                                    @else
                                        <a href="{{ route('mailing.downloadmanifest',$batch->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-download"></i> Download Manifest </a>
                                        <a href="{{ route('mailing.deletemanifest',$batch->id)}}" class="btn btn-danger btn-xs btn-delete-manifest"><i class="fa fa-times"></i> Delete Manifest </a>
                                    @endif
                                    </td>
                                    <td>
                                        @component('admin.mailing.components.deletemodal')
                                        @slot('id') 
                                            {{ $batch->id }}
                                        @endslot
                                        @endcomponent
                                        <a href="{{ route('mailing.show',$batch->id)}}" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> View Batch</a>
<?php
        $allcompleted=true;
        foreach ($batch->details as $detail){
            if (!isset($detail->recipient->work_order)) continue;
            $work=$detail->recipient->work_order;
            if($work->status <> 'completed') {
                 $allcompleted=false;break;
            }
        }
?>

                                        @if ($allcompleted)    
                                        <a class="btn btn-warning btn-xs" disabled><i class="fa fa-gears  fa-fw"></i> Complete Work Orders</a>
                                        @else
                                        <a href="{{ route('mailing.completeworkorder',$batch->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-gears  fa-fw"></i> Complete Work Orders</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="col-xs-12 text-center">
                            {{ $batches->links() }}
                        </div>
                        @else
                        <div class="col-xs-12">
                            <h5>No Mailing Batch found</h5>
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
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>

<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
    $('.btn-delete-manifest').click(function(){
        $('.btn-delete-manifest').addClass("disabled");
        $('.btn-delete-manifest').css('pointer-events','none');
    });
    
    $(":file").filestyle();
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $( '.uploadfile').submit( function(event){
        $(".filegroup p").remove();        
        var fe=$('input:file')[0].files[0].size;
        var max_uploadfileSize={{$max_uploadfileSize}};
        var file_name=$('input:file')[0].files[0].name;
        var ext=file_name.split('.').pop().toLowerCase();
        var ext_area=['pdf','jpeg','jpg','tiff','tif','doc','xls','docx','xlsx'];
        if (ext_area.indexOf(ext)==-1){
            $(".filegroup").append('<p>This file type is not permitted for upload.</p>');
            event.preventDefault();
        }
        
        if (fe>max_uploadfileSize){
            $(".filegroup").append('<p>This file is too large to upload.</p>');
            event.preventDefault();
        }
    });
    $('input:file').click( function(){
      $(".filegroup p").remove(); 
      $('.btn-success').removeClass("disabled");
      $('.btn-success').css('pointer-events','auto'); 
    });

});
</script>
@endsection