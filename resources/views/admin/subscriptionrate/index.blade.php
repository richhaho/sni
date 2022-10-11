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
                    <h1 class="page-header">Subscription Rate</h1>
                    <div class="pull-right">
                        <a href="{{route('subscriptionrate.download')}}" class="btn btn-warning"> <i class="fa fa-download"></i> Download CSV</a>
                        @component('admin.subscriptionrate.uploadmodal')
                        @endcomponent
                    </div>
                </div>
                @if (Session::has('message'))
                    <div class="col-xs-12 message-box">
                        <div class="alert {{ Session::get('message-class','alert-info') }}">{{ Session::get('message') }}</div>
                    </div>
                @endif
                    
                <div class="col-xs-12">
                    {!! Form::open(['route' => ['subscriptionrate.update'], 'method'=> 'POST','autocomplete' => 'off']) !!}
                        <div class="row" style="margin-top: 20px">
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Self-Service 30-day rate:</label>
                                <input type="number" name='self_30day_rate' class="form-control" value="{{$self_30day_rate}}" step="0.01" min="0">
                            </div>
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Self-Service 365-day rate:</label>
                                <input type="number" name='self_365day_rate' class="form-control" value="{{$self_365day_rate}}" step="0.01" min="0">
                            </div>
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Full-Service 30-day rate:</label>
                                <input type="number" name='full_30day_rate' class="form-control" value="{{$full_30day_rate}}" step="0.01" min="0">
                            </div>
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Full-Service 365-day rate:</label>
                                <input type="number" name='full_365day_rate' class="form-control" value="{{$full_365day_rate}}" step="0.01" min="0">
                            </div>
                            <div class="col-xs-12 col-md-12 form-group">
                                <button type="submit" class="btn btn-success btn-service-save pull-right"><i class="fa fa-save"> Save</i></button>
                            </div>
                        </div>                    
                    {!! Form::close() !!}
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