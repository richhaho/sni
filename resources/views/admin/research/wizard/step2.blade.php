@extends('admin.layouts.app')
@section('css')
<style>
    .tab-pane {
        margin-top: 20px;
    }
    .address_fields,.wait{
        display: none;
    }
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
{!! Form::open(['route' => ['research.wizard.step2.update',$job->id], 'method'=> 'PUT', 'id'=> 'wizard_form','autocomplete' => 'off']) !!}
  <div id="top-wrapper" >
    <div class="container-fluid">
      <div  class="col-xs-12">
          <h1 class="page-header">Research Wizard Step 2 
              <div class="pull-right">
                  <a class="btn btn-danger " href="{{ route('research.wizard.step1',$job->id) }}"><i class="fa fa-times-circle"></i> Back</a>
                  @if($job->type=='public')
                  <a class="btn btn-info " href="{{ route('research.wizard.step7',$job->id) }}"> Skip to Step 7</a>
                  @endif
              </div>
          </h1> 
          <div class="pull-right">
                <label>Research Sites</label>
                {!!  Form::select('match_job',$match_jobs,'', ['class' => 'form-control match_jobs']) !!}
          </div>   
      </div>
    </div>
  </div>
  <div id="page-wrapper" style="padding-top: 0px">
    <div class="container-fluid row" style="padding-right: 0px">
        <div class="col-xs-12 ">
            <span style="font-size: 22px"><strong>Does job address match tax roll address?  If no, is it close enough to be our job?</strong></span>
            <a class="btn btn-success " href="{{ route('research.wizard.step3',$job->id) }}"> Yes</a>
            <button class="btn btn-danger" type="submit" form="wizard_form"> No</button>
        </div>
      <div>&nbsp;</div>
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
      <div class="col-xs-12">
        <div class="panel panel-default">
          <div class="panel-body">
              <div class="row">
                <div class="col-md-6 form-group">
                    <label>Street Address:</label>
                    <input readonly name="address_1" value="{{ old("address_1",$job->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <label>Address2:</label>
                    <input readonly name="address_2" value="{{ old("address_2",$job->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <?php
                        $isCity = \App\PropertyRecords::where('property_county', $job->county)->where('owner_city', $job->city)->first();
                    ?>
                    <label class="text-danger">*City: @if(!$isCity) <span class="text-danger">Verify name of city to access correct county information.</span> @endif</label>
                    <input readonly name="city"  value="{{ old("city",$job->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>State / Province / Region:</label>
                    <input readonly id="states" value="{{ old("state",$job->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                </div>
                <div class="col-md-6 form-group">
                    <label>Zip code:</label>
                    <input readonly name="zip"  value="{{ old("zip",$job->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
              </div>
          </div>
      </div>
      </div>
    </div>
  </div>
{!! Form::close() !!}
@endsection


@section('scripts')
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>

$(function () {

    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
})
$(".match_jobs").change(function() {
    let url = $(".match_jobs").val();
    if (!url) return;
    url = url.includes('http') ? url : 'http://' + url;
    window.open(url, '_blank');
})
</script>   
@endsection