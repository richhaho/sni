@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .tab-pane {
        margin-top: 10px;
    }
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
<div id="top-wrapper" >
    <div class="container-fluid">
        <h1 class="page-header">To Do Uploads and Instructions<br>
            <div class="pull-right">
                @if($todo->status != 'completed')
                <a href="{{route('workorders.todo.complete', ['work_id' => $work->id, 'id' => $todo->id])}}" class="btn btn-primary"> Mark Complete</a>
                @endif
                <a class="btn btn-danger " href="{{ route('workorders.edit',$work->id)}}?#todos"><i class="fa fa-times"></i> Back</a>
            </div>
        </h1>
        <label>To Do Name:</label><span> {{$todo->name}}</span>&nbsp;&nbsp;&nbsp;<label>Completed:</label><span> {{$todo->completed_at ? $todo->completed_at : 'None'}}</span><br>
        <label>Description:&nbsp;</label><span> {{$todo->description}}</span><br>
        <label>Summary:&nbsp;&nbsp;&nbsp;&nbsp;</label><span> {{$todo->summary}}</span><br>
    </div>
</div>
<div id="page-wrapper">
    <div class="container-fluid">
        @if (count($errors) > 0)
        <div class="alert alert-danger">            
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="row">
            @if (Session::has('message'))
            <div class="col-xs-12 message-box">
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            </div>
            @endif
            
            @if($todo->todo_uploads)
            <div class="col-xs-6 panel panel-default">
                <div class="col-md-12">
                    <h3>Add attachment</h3>
                </div>
                {!! Form::open(['route' => ['workorders.todo.upload', $todo->id],'files' => true,'class'=>'uploadfile']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-xs-12 form-group filegroup">
                            <label>New File:</label>
                            {!!  Form::file('file','', ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-12 form-group">
                            <label>Description:</label>
                            {!!  Form::textarea('description','', ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-12 form-group ">
                            <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="col-md-12">
                    <div class="row">
                    @foreach ($todo->documents() as $attach)
                        <div class="col-md-3 text-center">
                            <div class="thumbnail">
                                <img class="img-responsive" src="{{ route('workorders.todo.document.showTodoThumbnail', $attach->id)}}" alt="{{ $attach->type }}">
                                <div class="caption">
                                    <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                    <p>{{ $attach->description }}</p>
                                        @component('admin.workorders.additionalservice.deletetododocumentmodal')
                                            @slot('id') 
                                                {{ $attach->id }}
                                            @endslot
                                            @slot('file_name') 
                                                {{ $attach->original_name }}
                                            @endslot
                                        @endcomponent
                                    <a href="{{ route('workorders.todo.document.download',$attach->id)}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Download</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    </div>
                </div>
            </div>
            @endif
            
            @if($todo->todo_instructions)
            <div class="col-xs-6 panel panel-default">
                <div class="col-md-12">
                    <h3>Provide Additional Instructions</h3>
                </div>
                {!! Form::open(['route' => ['workorders.todo.instruction', $todo->id]]) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-xs-12 form-group">
                            <label>Instruction:</label>
                            {!!  Form::textarea('instruction','', ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-xs-12 form-group ">
                            <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Submit</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>Instruction</td>
                                        <td>Entered</td>
                                        <td>Name</td>
                                        <td class="col-xs-2">Actions</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todo->instructions() as $instruction)
                                    <tr>
                                        <td>{{ $instruction->instruction}}</td>
                                        <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($instruction->created_at))->diffForHumans() }}</td>
                                        <td>@if (isset($instruction->writer()->full_name)){{ $instruction->writer()->full_name }} @else Deleted User @endif</td>
                                    
                                        <td>
                                        @component('admin.workorders.additionalservice.deletetodoinstructionmodal')
                                            @slot('id') 
                                                {{ $instruction->id }}
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
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>
<script>
$(function () {
    $(":file").filestyle();
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
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