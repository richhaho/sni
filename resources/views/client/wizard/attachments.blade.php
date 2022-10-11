@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
.stepwizard-step p {
    margin-top: 0px;
    color:#666;
}
.stepwizard-row {
    display: table-row;
}
.stepwizard {
    display: table;
    width: 100%;
    position: relative;
    pointer-events:none;
}

.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
}
.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}
.btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
}
@media screen and (max-width: 500px) {
    .stepwizard-step p {
        display: none !important;
    }
}
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
        <div id="top-wrapper" >
            <br>
            <div class="stepwizard">
              <div class="stepwizard-row setup-panel">
                  <div class="stepwizard-step col-xs-4">
                      <a type="button" class="btn btn-default btn-circle">1</a>
                      <p ><small>Job/Contract Information</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-4"> 
                      <a type="button" class="btn btn-default btn-circle">2</a>
                      <p><small>Job/Contract Parties</small></p>
                  </div>
                  <div class="stepwizard-step col-xs-4">    <a type="button" class="btn  btn-success btn-circle">3</a>
                      <p style="color: black"><small><strong>Attachments</strong></small></p>
                  </div>
              </div>
            </div> 
            <div class="container-fluid">
              <div class="row">
                <div class="col-xs-12">
                  <h3>
                    Attach Any Supporting Documents you may have.
                    <div class="pull-right">  
                      <a class="btn btn-danger " href="{{  route('wizard.getparties',$job->id) }}"><i class="fa fa-chevron-left"></i> Back</a> &nbsp;
                      <a  class="btn btn-success " href="{{ route('wizard.jobcreated',$job->id)}}">  Next <i class="fa fa-chevron-right"></i></a>&nbsp;&nbsp;&nbsp;
                    </div> 
                  </h3>  
                </div>
              </div>
              <div class="row">
                <div  class="col-xs-12">
                  <h5 class="page-header">
                     Please upload any documents you may have such as the NOC for the job, a copy of your contract with your customer, or any other supporting documents that could help us fully prepare your notice.
                  </h5>  
                </div>
              </div>
            </div>
        
            <div class="container-fluid">
              @if (count($errors) > 0)
                <div class="row">
                  <div class="col-xs-12 col-md-12 message-box">
                    <div class="alert alert-danger">
                      @foreach ($errors->all() as $error)
                          {{ $error }}
                      @endforeach
                    </div>
                  </div>
                </div>
              @endif
              <div class="row">
                <div class="col-xs-12 col-md-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">Job Attachments </div>
                    <div class="panel-body">
                    {!! Form::open(['route' => ['wizard.addattachments',$job->id],'files' => true,'class'=>'uploadfile']) !!} 
                    {!! Form::hidden('attach_to','job') !!}
                    {!! Form::hidden('to_id',$job->id) !!}
                      <div class="col-xs-12 form-group filegroup">
                          <label>New File:</label>
                          {!!  Form::file('file','', ['class' => 'form-control']) !!}
                      </div>
                      <div class="col-xs-12 form-group">
                          <label>Type:</label>
                          {!!  Form::select('type',$attachment_types,'notice-of-commencement', ['class' => 'form-control']) !!}
                      </div>
                      <div class="col-xs-12 form-group">
                          <label>Description:</label>
                          {!!  Form::textarea('description','', ['class' => 'form-control','rows'=>4]) !!}
                      </div>
                       <div class="col-xs-12 form-group ">
                          <button class="btn btn-success pull-right " type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                      </div>
                    {!! Form::close() !!}
                          <div class="col-xs-12">
                              @foreach ($job->attachments as $attach)
                                   @if($loop->first)
                                      <div class="row">
                                  @endif
                                  <div class="col-md-4 text-center">
                                       <div class="thumbnail">
                                            <a href="{{ route('wizard.jobs.showattachment',[$job->id,$attach->id])}}">
                                          <img class="img-responsive" src="{{ route('wizard.showthumbnail',[$attach->id])}}" alt="{{ $attach->type }}">
                                            </a>
                                          <div class="caption">
                                            <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                            <p>{{ $attach->description }}</p>
                                            @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                                            <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                                            @endif
                                            <p>
                                              @component('client.wizard.components.deleteattachmentmodal')
                                                  @slot('jid') 
                                                      {{ $job->id }}
                                                  @endslot
                                                  
                                                  @slot('id') 
                                                      {{ $attach->id }}
                                                  @endslot
                                                  @slot('file_name') 
                                                      {{ $attach->original_name }}
                                                  @endslot
                                              @endcomponent
                                            </p>
                                          </div>
                                        </div>
                                  </div>
                                   @if($loop->iteration % 3 == 0 && $loop->last)
                                      </div>
                                   @else
                                      @if($loop->iteration % 4 == 0)
                                          </div>
                                          <div class="row">
                                      @else
                                          @if($loop->last)
                                             </div>
                                          @endif
                                      @endif
                                  @endif
                              @endforeach
                          </div>
                  </div>
                </div>
              </div>
            </div>  
        </div>
   
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-success').click(function(){
    $('.btn-success').addClass("disabled");
    $('.btn-success').css('pointer-events','none');
}); 
$(function () {
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
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();
 
});
</script>
    
@endsection