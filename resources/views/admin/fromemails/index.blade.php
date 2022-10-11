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
                    <h1 class="page-header">From Emails Setting</h1>
                </div>
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                        <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                    </div>
                @endif
                    
                <div class="col-xs-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Email Name</th>
                                <th>From Email</th>
                                <th>From Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($froms as $from)
                            {!! Form::open(['route' => ['fromemails.update',$from->id], 'method'=> 'POST','autocomplete' => 'off']) !!}
                            <tr>
                                <td> {{$from->name}}</td>
                                <td>
                                    <input type="email" name="from_email" class="noucase form-control from_email_{{$from->id}}" value="{{$from->from_email}}" required readonly>
                                </td>
                                <td>
                                    <input type="text" name="from_name" class="noucase form-control from_name_{{$from->id}}" value="{{$from->from_name}}" required readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-xs btn-edit btn-edit-{{$from->id}}" data="{{$from->id}}"><i class="fa fa-edit"></i> Edit</button>
                                    <button type="submit" class="btn btn-success btn-xs btn-save hidden btn-save-{{$from->id}}"><i class="fa fa-save"></i> Save</button>
                                </td>
                            </tr>
                            {!! Form::close() !!}
                            @endforeach
                        </tbody>
                    </table>
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
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('.btn-edit').click(function(){
        const id = $(this).attr('data');
        $('.btn-save-'+id).removeClass('hidden');
        $(this).addClass('hidden');
        $('.from_name_'+id).attr('readonly', false);
        $('.from_email_'+id).attr('readonly', false);
    });
});
</script>
@endsection