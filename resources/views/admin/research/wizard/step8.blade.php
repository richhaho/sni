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
  <div id="top-wrapper" >
    <div class="container-fluid">
      <div  class="col-xs-12">
          <h1 class="page-header">Research Wizard Step 8 
              <div class="pull-right">
                  <a class="btn btn-danger " href="{{ route('research.wizard.step7',$job->id) }}"><i class="fa fa-times-circle"></i> Back</a>
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
        {!! Form::open(['route' => ['research.wizard.step8.update',$job->id], 'method'=> 'PUT', 'id'=> 'wizard_form','autocomplete' => 'off']) !!}
        <div class="col-xs-12 ">
            <span style="font-size: 18px"><strong>Is the orderby (customer) the same as the GC or working for the GC? If it is already pre filled or if you found the NOC with the orderby or GC's name, then click YES.</strong></span>
            <a class="btn btn-success " href="{{ route('research.wizard.step9',$job->id) }}"> Yes</a>
            <button class="btn btn-danger" type="submit" form="wizard_form"> No</button>
        </div>
        {!! Form::close() !!}
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
        <div class="row">
        @foreach ($parties_type as $type_key => $type_name)
            <div class="col-md-12 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">{{ $type_name }} @if ($type_name=="Client") (Your Company) @endif</h4>
                    </div>
                    <div class="panel-body">
                        @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 
                
                            @include('admin.research.wizard.component.contacticon')

                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
        </div>
      </div>
    </div>
  </div>
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