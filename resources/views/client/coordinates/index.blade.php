@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('css')
<style>
    #filters-form {
        margin-bottom: 15px;
        margin-top: 15px;
    }
</style>
@endsection


@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    Saved Coordinates
                    <a class="btn btn-success pull-right" href="#" data-toggle="modal" data-target="#modal-coordinate-create" onclick="detectGPS_Coordinates()"><i class="fa fa-plus"> </i> Add Coordinates</a>
                </h1>
            </div>
            @component('client.coordinates.components.createmodal')
            @endcomponent
            <div class="col-xs-12" id="filters-form">
                {!! Form::open(['route' => 'client.coordinates.setfilter', 'class'=>'form-inline'])!!}
                    <div class="form-group">
                        <label for="coordinate_name_filter"> Coordinate Name: </label>
                        {!! Form::text('coordinate_name',session('coordinate_filter.name'),['class'=>'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <label for="work_condition"> Used/Unused on Job: </label>
                        {!! Form::select('usedonjob',$usedonjob,session('coordinate_filter.usedonjob'),['class'=>'form-control'])!!}
                    </div>
                <button class="btn btn-success" type="submit" ><i class="fa fa-filter"></i> Enter</button>
                <a href="{{ route('client.coordinates.resetfilter') }}" class="btn btn-danger">Clear</a>
                {!! Form::close() !!}
            </div>
            @if (Session::has('message'))
                <div class="col-xs-12 message-box">
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                </div>
            @endif
                
            @if(count($coordinates) > 0 )
            <div class="col-xs-12" style="overflow-x: scroll; padding-bottom:100px">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Coordinate Name</th>
                            <th class="text-center">Latitude</th>
                            <th class="text-center">Longitidue</th>
                            <th class="text-center">Job Counts</th>
                            <th class="col-xs-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coordinates as $coordinate)
                        <tr>
                            <td align="center"> {{ $coordinate->name }}</td>
                            <td align="center"> {{ $coordinate->lat }}</td>
                            <td align="center"> {{ $coordinate->lng }}</td>
                            <td align="center"> {{ count($coordinate->jobs()) }}</td>
                            <td align="center">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="{{route('client.jobs.index')}}?coordinate_id={{$coordinate->id}}"><i class="fa fa-briefcase"></i> Used Jobs</a></li>
                                        @if(count($coordinate->jobs())==0)
                                        <li><a href="#" data-toggle="modal" data-target="#modal-coordinate-edit-{{$coordinate->id}}"><i class="fa fa-pencil"></i> Edit</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="#" data-toggle="modal" data-target="#modal-coordinate-delete-{{ $coordinate->id }}" class="close-a"><i class="fa fa-remove"></i> Delete</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                            @component('client.coordinates.components.editmodal')
                            @slot('id') 
                                {{ $coordinate->id }}
                            @endslot
                            @slot('name') 
                                {{ $coordinate->name }}
                            @endslot
                            @slot('lat') 
                                {{ $coordinate->lat }}
                            @endslot
                            @slot('lng') 
                                {{ $coordinate->lng }}
                            @endslot
                            @endcomponent

                            @component('client.coordinates.components.deletemodal')
                            @slot('id') 
                                {{ $coordinate->id }}
                            @endslot
                            @slot('name') 
                                {{ $coordinate->name }}
                            @endslot
                            @endcomponent
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12 text-center">
                {{ $coordinates->links() }}
            </div>
            @else
            <div class="col-xs-12">
                <h5>No coordinates found</h5>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/geoLocation/helper.js') }}" type="text/javascript"></script>
<script>
$(function () {
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
});
function detectGPS_Coordinates() {
    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        $('.detect_status').text('');
        $('.coordinate_x').val(latitude);
        $('.coordinate_y').val(longitude);
    }
    function error() {
        $('.detect_status').text('Unable to retrieve your location');
    }

    if (!navigator.geolocation) {
        $('.detect_status').text('Geolocation is not supported by your browser');
    } else {
        $('.detect_status').text('Locating…');
        // navigator.geolocation.getCurrentPosition(success, error);
        getCoordinates(success, error);
    }
}
</script>
@endsection