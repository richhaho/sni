@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
   
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">FTP Servers
                    <div class="pull-right">
                         <a class="btn btn-success pull-right" href="{{ route('serversftp.create')}}"><i class="fa fa-plus"></i> Add Server</a>
                        
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
                                FTP Servers
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Host</th>
                                        <th>User</th>
                                        <th>Password</th>
                                        <th>Path</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($servers) > 0)
                                        @foreach($servers as $s)
                                        <tr>
                                            <td>{{ $s->ftp_name}}</td>
                                            <td>{{ $s->ftp_host}}</td>
                                            <td>{{ $s->ftp_user}}</td>
                                            <td>{{ $s->ftp_password}}</td>
                                            <td>{{ $s->ftp_path}}</td>
                                            <td>   
                                                @component('admin.ftpservers.components.deletemodal')
                                                    @slot('id') 
                                                        {{ $s->id }}
                                                    @endslot
                                                    @slot('ftp_name') 
                                                        {{ $s->ftp_name }}
                                                    @endslot
                                                @endcomponent
                                                <a href="{{ route('serversftp.edit',$s->id)}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3">No Servers Available</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            
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