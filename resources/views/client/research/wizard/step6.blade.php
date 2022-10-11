@extends('client.layouts.app')
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
    @include('client.navigation')
@endsection

@section('content')
{!! Form::open(['route' => ['client.research.wizard.step6.update',$job->id], 'method'=> 'PUT', 'id'=> 'wizard_form','autocomplete' => 'off']) !!}
  <div id="top-wrapper" >
    <div class="container-fluid">
      <div  class="col-xs-12">
          <h1 class="page-header">Research Wizard Step 6 
              <div class="pull-right">
                  <a class="btn btn-danger " href="{{ route('client.research.wizard.step5',$job->id) }}"><i class="fa fa-times-circle"></i> Back</a>
                  @if($job->type=='public')
                  <a class="btn btn-info " href="{{ route('client.research.wizard.step7',$job->id) }}"> Skip to Step 7</a>
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
            <span style="font-size: 22px"><strong>Does the Folio/Parcel ID on Tax Roll match the Folio/Parcel Id on the NOC?</strong></span>
            <a class="btn btn-success " href="{{ route('client.research.wizard.step7',$job->id) }}"> Yes</a>
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
                <div class="col-md-12 form-group">
                    <label>Folio Number:</label>
                    {!!  Form::text('folio_number',old("folio_number", $job->folio_number), ['class' => 'form-control', 'readonly'=>true]) !!}
                </div>
              </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Attachments:</label>
                </div>
            </div>
            <div class="row">
            @foreach ($job->attachments as $attach)
                <div class="col-md-3 text-center">
                    <div class="thumbnail">
                        <a href="{{ route('client.research.showattachment',[$job->id,$attach->id])}}">
                            <img class="img-responsive" src="{{ route('client.research.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                        </a>
                    </div>
                    <div class="caption">
                        <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                        <p>{{ $attach->description }}</p>
                        @if ($attach->clientviewable != 'yes')
                        <p><strong>Hidden</strong></p>
                        @endif
                        @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                        <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
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