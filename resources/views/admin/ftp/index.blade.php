@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
   
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">FTP Locations
                    <div class="pull-right">
                        
                        
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
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                    </div>
                @endif
                <div class="row">
                  
                    
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                FTP Locations
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Server Name</th>
                                        <th>Path</th>
                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($paths) > 0)
                                        @foreach($paths as $p)
                                        <tr>
                                            <td>{{ $p->name}}</td>
                                            <td>{{ $p->server->ftp_name}}</td>
                                            <td>{{ $p->path}}</td>
                                            <td>   
                                                @component('admin.ftp.components.deletemodal')
                                                    @slot('id') 
                                                        {{ $p->id }}
                                                    @endslot
                                                    @slot('path_name') 
                                                        {{ $p->name }}
                                                    @endslot
                                                @endcomponent
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3">No Paths Available</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="panel-footer clearfix inline-form">
                                 {!! Form::open(['route' => ['ftp.store'], 'method'=> 'POST','autocomplete' => 'off']) !!}
                                <div class="col-xs-3 form-group">
                                    <label>Name:</label>
                                    <input name="name"  value="{{ old("name")}}" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                  <div class="col-xs-3 form-group">
                                    <label>Server:</label>
                                    {!! Form::select('connection_id',$servers,'',['class'=>'form-control'])!!}
                                </div>
                                 <div class="col-xs-3 form-group">
                                    <label>Path:</label>
                                    <input name="path"  value="{{ old("path")}}" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-xs-3">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-success btn-block" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                                </div>
                                 {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    
               
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
  $('[data-toggle="tooltip"]').tooltip()
$(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });

})
</script>
    
@endsection