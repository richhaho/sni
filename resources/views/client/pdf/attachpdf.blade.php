@extends('client.layouts.app')


@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .tab-pane {
        margin-top: 10px;
    }
    .attach_group{
        border: 1px solid #ddd;
        margin-left: 20px;
         

    }

</style>
@stop


@section('content')
<div class="row" >
    <div class="container-fluid">
        <div  class="col-xs-12">
            <center><h1 class="page-header">Attach PDF<br></h1></center>       
        </div>
    </div>
</div>
<br>
{!! Form::open(['route'=>['client.notices.edit',$id],'method'=> 'GET','autocomplete' => 'off','class' => 'form-inline'])!!}
{{ Form::hidden('fromAttachPDF', 'true',['id' =>'fromAttachPDF']) }}
<div class="container-fluid">
<div class="col-md-12 col-lg-12 col-sm-12 ">
    <button type="submit" class="btn btn-danger button-generate pull-right" name="generate" value="preview"><i class="fa fa-eye-slash"></i> Cancel</button>
</div>
</div>
{!! Form::close() !!}
<br>

                        @if (Session::has('message'))
                            <div class="col-xs-12 message-box">
                            <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                            <?php Session::flash('message',null); ?>
                            </div>
                        @endif
<div class="row">
    {!! Form::open(['route' => ['client.pdfpage.AttachPDF',$id],'files' => true]) !!}
    <div class="col-md-5 col-lg-5 col-sm-12">
        <div class="col-md-12 col-lg-12 col-xm-12">
            <center><h2>Upload PDF file</h2></center>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12  attach_group">

            <br>
            {{ Form::hidden('attach_method', 'upload') }}
            <div class="col-md-12 form-group">
                <label>New File:</label>
                 
                <input type="file" name="file" accept="application/pdf" class="form-control">
            </div>
            
            <div class="col-md-12 form-group">
                <div style="float: left !important;margin-top: 10px;margin-right: 10px;">Place Behind Notice</div>
                <div class="checkbox checkbox-slider--b-flat"  style="float: left !important;">
                    <label>
                    <input name="placeType" type="checkbox" ><span >Use in Place of Notice</span>
                    </label>
                </div>
             </div>    
             <div class="col-xs-12 form-group ">
                <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Upload and Next</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    {!! Form::open(['route' => ['client.pdfpage.AttachPDF',$id],'autocomplete' => 'off','class' => 'attachPDF']) !!}
    <div class="col-md-7 col-lg-7 col-sm-12">
        <div class="col-md-12 col-lg-12 col-xm-12">
            <center><h2>Attach from attachment of work order and job.</h2></center>
        </div>
        <div class="col-md-11 col-lg-11 col-sm-12  attach_group">
            <br>
            {{ Form::hidden('attach_method', 'from_attachment') }}
            <div class="col-md-12 form-group">
                <label>Select Attachment:</label>
                {!!  Form::select('file_path',$attachment_files,$attachment_files, ['class' => 'form-control file_path']) !!}
            </div>
            
            <div class="col-md-12 form-group">
                <div style="float: left !important;margin-top: 10px;margin-right: 10px;">Place Behind Notice</div>
                <div class="checkbox checkbox-slider--b-flat"  style="float: left !important;">
                    <label>
                    <input name="placeType" type="checkbox" ><span >Use in Place of Notice</span>
                    </label>
                </div>
             </div>    
             <div class="col-xs-12 form-group ">
                <button class="btn btn-success pull-right nextwithPDF" type="submit"> <i class="fa fa-floppy-o"></i> Next with PDF</button>
            </div>
        </div>
              
       
    </div>
    {!! Form::close() !!}
</div>
            
@endsection

@section('scripts')
<script type="text/javascript">
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    if (!$('.file_path').val()){
        $('.nextwithPDF').addClass('disabled');
    }
    $( '.attachPDF').submit( function(event){
        if (!$('.file_path').val()){
            event.preventDefault();
        }
    });
</script>
@endsection