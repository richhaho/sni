@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection
@section('css')
<style>
.new-template {
  display:none;
}
.new-item-line {
    display:none;
}
.delete-line {

}
</style>
@endsection
@section('content')
    {!! Form::open(['route' => ['serversftp.store'],'autocomplete' => 'off']) !!}
  
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Create New FTP Server
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                 
                            <a class="btn btn-danger " href="{{ route('serversftp.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;

                    </div>
                </h1>       
            </div>
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
                  
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>FTP Name:</label>
                                   {{ Form::text('ftp_name','',['class'=>'form-control noucase'])}}
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>FTP Host:</label>
                                   {{ Form::text('ftp_host','',['class'=>'form-control noucase'])}}
                                    
                                </div>
                               
                                </div>
                                <div class="row">
                                <div class="col-xs-6 form-group">
                                    <label>FTP User:</label>
                                   {{ Form::text('ftp_user','',['class'=>'form-control noucase'])}}
                                </div>
                                <div class="col-xs-6 form-group">
                                    <label>FTP Password:</label>
                                   {{ Form::text('ftp_password','',['class'=>'form-control noucase'])}}
                                    
                                </div>
                               
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>FTP Path</label>
                                   {{ Form::text('ftp_path','',['class'=>'form-control'])}}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                 
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
   
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
    
    
 
@endsection

@section('scripts')
<script>
var contador = 1;
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

  

})
</script>
    
@endsection