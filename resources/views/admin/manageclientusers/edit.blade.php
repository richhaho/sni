@extends('admin.layouts.app')

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => ['clientusers.update',$client->id,$user->id], 'method'=> 'PUT','autocomplete' => 'off']) !!}
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit User {{ $user->name }}
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('clientusers.index',$client->id)}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                                User Info
                            </div>
                            <div class="panel-body">
                                
                                <div class="row">
                                <div class="col-xs-12 col-md-6 form-group">
                                    <label>First Name:</label>
                                    <input name="first_name"  value="{{ old("first_name",$user->first_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                
                                <div class="col-xs-12 col-md-6 form-group">
                                    <label>Last Name:</label>
                                    <input name="last_name" value="{{ old("last_name",$user->last_name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-xs-12 col-md-6 form-group">
                                    <label>Password:</label>
                                    <input name="new_password"  value="" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                
                                <div class="col-xs-12 col-md-6 form-group">
                                    <label>Confirm Password:</label>
                                    <input name="new_password_confirmation" value="" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 col-md-4 form-group">
                                    <label>Email:</label>
                                    <input name="email" value="{{ old("email",$user->email)}}" class="form-control noucase" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                            
                                <div class="col-xs-12  col-md-4 form-group">
                                    <label>Status:</label>
                                    {!!  Form::select('status',['1'=> 'Enabled', '0' =>'Disabled'],old("status",$user->status), ['class' => 'form-control']) !!}
                                </div>
                                    
                                     <div class="col-xs-12  col-md-4 form-group">
                                    <label>Email Verified:</label>
                                    {!!  Form::select('verified',['1'=> 'Yes', '0' =>'No'],old("verified",$user->verified), ['class' => 'form-control']) !!}
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
  
$(function () {
  $('[data-toggle="tooltip"]').tooltip()

  
})
</script>
    
@endsection