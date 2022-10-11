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
                    <h1 class="page-header">Research Sites Setting
                    @component('admin.sites.components.createmodal')
                    @endcomponent
                    </h1>
                </div>
                <div class="col-xs-12">
                    {!! Form::open(['route' => 'sites.setfilter', 'class'=>'form-inline'])!!}
                        <div class="form-group">
                            <label for="job_name_filter"> County: </label>
                            {!! Form::text('county',session('sites.county'),['class'=>'form-control'])!!}
                        </div>
                        <div class="form-group">
                            <label for="job_name_filter"> Site Name: </label>
                            {!! Form::text('site_name',session('sites.site_name'),['class'=>'form-control'])!!}
                        </div>
                        <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                        <a href="{{ route('sites.resetfilter') }}" class="btn btn-danger">Clear</a>
                    {!! Form::close() !!}
                </div>
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                        <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                    </div>
                @endif
                @if(count($sites) > 0 )    
                <div class="col-xs-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>County Name</th>
                                <th>Site Name</th>
                                <th>Site URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sites as $site)
                            {!! Form::open(['route' => ['sites.update',$site->id], 'method'=> 'POST','autocomplete' => 'off']) !!}
                            <tr>
                                <td>
                                    <input type="text" name="county" class="form-control county_{{$site->id}}" value="{{$site->county}}" required readonly>
                                </td>
                                <td>
                                    <input type="text" name="name" class="form-control name_{{$site->id}}" value="{{$site->name}}" required readonly>
                                </td>
                                <td>
                                    <input type="text" name="url" class="form-control url_{{$site->id}}" value="{{$site->url}}" required readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-xs btn-edit btn-edit-{{$site->id}}" data="{{$site->id}}"><i class="fa fa-edit"></i> Edit</button>
                                    <button type="submit" class="btn btn-success btn-xs btn-save hidden btn-save-{{$site->id}}"><i class="fa fa-save"></i> Save</button>
                                    @component('admin.sites.components.deletemodal')
                                        @slot('id') 
                                            {{ $site->id }}
                                        @endslot
                                    @endcomponent
                                </td>
                            </tr>
                            {!! Form::close() !!}
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12 text-center">
                    {{ $sites->links() }}
                </div>
                @else
                <div class="col-xs-12">
                    <h5>No Sites found</h5>
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
 
 
<script>
    
    
$(function () {
    $(".message-box").fadeTo(3000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('.btn-edit').click(function(){
        const id = $(this).attr('data');
        $('.btn-save-'+id).removeClass('hidden');
        $(this).addClass('hidden');
        $('.county_'+id).attr('readonly', false);
        $('.name_'+id).attr('readonly', false);
        $('.url_'+id).attr('readonly', false);
    });
});
</script>
@endsection