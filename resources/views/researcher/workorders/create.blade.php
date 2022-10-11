@extends('researcher.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => 'workorders.store','autocomplete' => 'off']) !!}
        {!! Form::hidden('status','open')!!}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Work Order
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ route('workorders.index')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
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
                               Details
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::select('job_id',$jobs_list,old("job_id",$job_id), ['class' => 'form-control','id'=>'job_id']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-3 form-group">
                                        <label>Job Number:</label>
                                        {{ Form::text('job_number','',['class'=>'form-control','disabled' => true,'id'=>'job_number']) }}
                                    </div>
                                    <div class="col-xs-12 col-md-3 form-group">
                                        <label>Job Contract Amount:</label>
                                       
                                        {{ Form::text('job_contract_amount','',['class'=>'form-control','disabled' => true,'id'=>'job_contract_amount']) }}
                                    </div>
                                </div>
                                <div class="row">
                                   
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Work Order Type:</label>
                                        {!!  Form::select('type',$wo_types,old("type","notice-to-owner"), ['class' => 'form-control','id'=>'work_order_type']) !!}
                                    </div>
                                     
                                      <div class="col-xs-12 col-md-6 form-group">
                                        <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="is_rush" type="checkbox"><span>Is Rush?</span>
                                        </label>
                                    </div>
                                    </div>
                                
                                </div>
                              
                                 <div class="row jobs_last_day hidden">
                                        <div class="col-xs-12 col-md-6 form-group">
                                            <label>Last Day on Job:</label>

                                             <input disabled required="true" id="last_day" name="last_day"  value="{{ old("last_day") }}"  data-date-autoclose="true" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                     

                                        </div>  
                                    </div>
                                
                                 <div class="row">   
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Due Date:</label>

                                        <input  required="true" name="mailing_at"  value="{{ old("mailing_at") }}" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                   
                                        
                                    </div>  
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Max. Mailing  Date:</label>
                                
                                        <input  required="true" name="due_at"  value="{{ old("due_at") }}" class="form-control date-picker"  data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                             
                                        
                                    </div>
                                
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    
        
                </div>
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
    
    <div class="modal fade" tabindex="-1" role="dialog" id="existentType">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Existent Type</h4>
      </div>
      <div class="modal-body">
        <p>This Job already has a notice of this type. </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
});
var job_started_at;
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
 
  $('.date-picker').datepicker();
 
  $('#job_id').select2();
  
  $('#job_id').on('change', function () {
      var job_id = $(this).val();
      getJobNumber(job_id);
      getContractAmount(job_id);
      getStartedAt(job_id);
      getLastDay(job_id);
  });
  
  var job_id = $('#job_id').val();
      getJobNumber(job_id);
      getContractAmount(job_id);
      getStartedAt(job_id);
      getLastDay(job_id);
      
  $("select[name='type']").on('change',function() {
      
      var type = $(this).val();
      
      $.get('{{ url('researcher/jobs/')}}' + '/' + job_id + '/type/' + type, function(data) {
         if(data=="YES") {
             $('#existentType').modal('show')
         } 
      });
      
      $('div.jobs_last_day').addClass('hidden');
      $("input[name='last_day']").attr('disabled', true);
      if (type == 'claim-of-lien' || type == 'notice-of-non-payment') {
            $('div.jobs_last_day').removeClass('hidden');
            $("input[name='last_day']").attr('disabled', false);
            var last_day = $("input[name='last_day']").val()
            if (last_day.length > 0){
                calculateDates(type)
            }else {

            }
      } else {
            calculateDates(type)
      }
  });
  
  $("input[name='last_day']").on('change', function (){
        var type = $("select[name='type']").val();
        var last_day = $("input[name='last_day']").val()
        if (last_day.length > 0) {
            calculateDates(type)
        }else {

        }
  });    
   
   var job_id = $('#job_id').val();
   $.get('{{url("/researcher/jobs")}}/' + job_id + '/startedat', function( data ) {
         job_started_at =  data ;
          $("select[name='type']").trigger('change');   
    });
   
      
  function getJobNumber(job_id) {
    $.get('{{url("/researcher/jobs")}}/' + job_id + '/number', function( data ) {
         $( "#job_number" ).val( data );
    });
   }
   
   function getContractAmount(job_id) {
    $.get('{{url("/researcher/jobs")}}/' + job_id + '/contractamount', function( data ) {
         $( "#job_contract_amount" ).val( data );
    });
   }
   
   function getStartedAt(job_id) {
    $.get('{{url("/researcher/jobs")}}/' + job_id + '/startedat', function( data ) {
         job_started_at =  data ;
    });
   }
   
   function getLastDay(job_id) {
       $( "#last_day" ).val('');
    $.get('{{url("/researcher/jobs")}}/' + job_id + '/lastday', function( data ) {
         $( "#last_day" ).val( data );
    });
   }

});

function calculateDates(type) {
      $("input[name='is_rush']").attr('checked', false); // Checks it
      $("input[name='due_at']").val('');
      $("input[name='mailing_at']").val('');
      
      var stdt = moment(job_started_at);
      var today = moment();
      var last_day = $("input[name='last_day']").val();
      
      
      
      
      
      if (type=='notice-to-owner' ) {

           var dif = today.diff(stdt, 'days')
           if (dif >= 36) {
                $("input[name='is_rush']").attr('checked', true); // Checks it
                $("input[name='is_rush']").attr('disabled', true); 
           }
           var due_at = moment(job_started_at).add(43,'days');
           var mailing_at = moment(job_started_at).add(39,'days');
           
      } else {
           if (type == 'claim-of-lien' || type == 'notice-of-non-payment') {
                if (last_day.length > 0) {
                     var stdt = moment(last_day,'MM/DD/YYYY');
                     var dif = today.diff(stdt, 'days')
                    if (dif >= 86) {
                         $("input[name='is_rush']").attr('checked', true); // Checks it
                         $("input[name='is_rush']").attr('disabled', true); 
                    }
                    var due_at = moment(last_day).add(89,'days');
                    var mailing_at = moment(last_day).add(89,'days');
                 } else {

                 }   
            } else {
                var dif = today.diff(stdt, 'days')
                 if (dif >= 4) {
                      $("input[name='is_rush']").attr('checked', true); // Checks it
                      $("input[name='is_rush']").attr('disabled', true); 
                 }
                var due_at = moment().add(10,'days');
                var mailing_at = moment().add(7,'days');
            }
      }
      
      $("input[name='due_at']").val(due_at.format('MM/DD/YYYY'));
      $("input[name='mailing_at']").val(mailing_at.format('MM/DD/YYYY'));
  }
</script>
    
@endsection
