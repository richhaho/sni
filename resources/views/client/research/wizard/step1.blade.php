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
{!! Form::open(['route' => ['client.research.wizard.step1.update',$job->id], 'method'=> 'PUT', 'id'=> 'wizard_form','autocomplete' => 'off']) !!}
  {!!  Form::hidden('isSave', '', ['class' => 'form-control is-save']) !!}
  <div id="top-wrapper" >
    <div class="container-fluid">
      <div  class="col-xs-12">
          <h1 class="page-header">Research Wizard Step 1 
              <div class="pull-right">
                  <a class="btn btn-danger " href="{{ route('client.research.edit', $job->id) }}"><i class="fa fa-times-circle"></i> Back</a>
                  <button class="btn btn-success " type="submit" form="wizard_form"> <i class="fa fa-floppy-o"></i> Next</button>
              </div>
          </h1>
          <div class="pull-right">
                <label>Research Sites</label>
                {!!  Form::select('match_job',$match_jobs,'', ['class' => 'form-control match_jobs']) !!}
          </div>
      </div>
      <div  class="col-xs-12">
        <span style="font-size: 18px"><strong>Verify the folio and legal description: By clicking into Research Site Property Appraiser.  If found, PDF copy to attach on Wizard Step 4</strong></span>
      </div>
    </div>
  </div>
  <div id="page-wrapper" style="padding-top: 5px">
    <div class="container-fluid row" style="padding-right: 0px">
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
          <div class="panel-heading">
            <h4 class="panel-title">Folio and legal description
            <button class="btn btn-success btn-xs pull-right btn-save" type="button"> <i class="fa fa-floppy-o"></i> Save</button>
            </h4>
          </div>
          <div class="panel-body">
              <div class="row">
                <div class="col-md-12 form-group">
                    <label>County:</label>
                    <input id="counties" name="county"  value="{{ old("county",$job->county)}}" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <label>Street Address:</label>
                    <input name="address_1" value="{{ old("address_1",$job->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <label>Address2:</label>
                    <input name="address_2" value="{{ old("address_2",$job->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <?php
                        $isCity = \App\PropertyRecords::where('property_county', $job->county)->where('owner_city', $job->city)->first();
                    ?>
                    <label class="text-danger">*City: @if(!$isCity) <span class="text-danger">Verify name of city to access correct county information.</span> @endif</label>
                    <input name="city"  value="{{ old("city",$job->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>State / Province / Region:</label>
                    <input id="states" value="{{ old("state",$job->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                </div>
                <div class="col-md-6 form-group">
                    <label>Zip code:</label>
                    <input name="zip"  value="{{ old("zip",$job->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                </div>
                <div class="col-md-6 form-group">
                    <label>Folio Number:</label>
                    {!!  Form::text('folio_number',old("folio_number", $job->folio_number), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <label>Legal Descriptions:</label> <a class="cleanup" data-id="legal-description" href="#">Clean Up</a>
                    {!!  Form::textarea('legal_description',old("legal_description",$job->legal_description), ['class' => 'form-control','id'=> 'legal-description']) !!}
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
    $('.btn-save').click(function() {
        $('.is-save').val('yes');
        $('#wizard_form').submit();
    });
})

$(".match_jobs").change(function() {
    let url = $(".match_jobs").val();
    if (!url) return;
    url = url.includes('http') ? url : 'http://' + url;
    window.open(url, '_blank');
    $(".match_jobs").val('');
})
$('body').on('click','.cleanup',function() {
    var xid = $(this).data('id');
    var txtValue = $('#' + xid).val();
    var Stext = txtValue.replace(/\n|\r/g, " ").replace(/\n/g, " ").replace(/\r/g, " ");
    $('#' + xid).val(Stext);
});
</script>   
@endsection