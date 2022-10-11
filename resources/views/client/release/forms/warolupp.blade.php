@extends('client.release.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-12">
            <div class="checkbox checkbox-slider--b-flat">
                <label>
                <input name="show_below_title" type="checkbox" class="show-below-title"><span> Check here if you are being asked to supply a Final Waiver even though you have NOT been paid in full</span>
                </label>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Additional information that will appear on your Waiver </label>
                {!! Form::text('below_title',$below_title,['class'=>'form-control noucase below-title-group'])!!}
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Dated On: </label>
            {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
         <div class="col-xs-4">
        <div class="form-group">
            <label>Paid Through Date: </label>
            {!! Form::text('date_paid',$date_paid,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Amount: </label>
            {!! Form::number('amount',$amount,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
        </div>
        </div>
    </div>
    <div class="row ">    
        <div class="col-xs-6">
         <div class="form-group">
            <label>Customer (Order by) Name: </label>
            {!! Form::text('customer_name',$customer_name,['class'=>'form-control'])!!}
        </div>
        </div>
        
        <div class="col-xs-6">
         <div class="form-group">
            <label>Property Owner: </label>
            {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    </div>

    

   
     <div class="col-xs-6">
         <div class="row">
            <div class="form-group">
                <label>Job Number: </label>
                {!! Form::text('nto_number',$nto_number,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Name: </label>
                {!! Form::text('job_name',$job_name,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Address: </label>
                {!! Form::textarea('job_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$job_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
         <div class="row">
            <div class="form-group">
                <label>Job County: </label>
                {!! Form::text('job_county',$job_county,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Legal Description: </label>
                {!! Form::textarea('job_legal',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$job_legal),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="col-xs-12">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-12">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        
        <div class="col-xs-12">
            <div class="form-group">
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
            </div>
        </div>
  
    
        
        <div class="col-xs-12">
            <div class="form-group">
                <label>Client Address: </label>
                {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
         <div class="col-xs-12">
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="col-xs-12">
        <div class="form-group">
            <label>Signed/Sworn Date: </label>
            {!! Form::text('sworn_signed_at',$sworn_signed_at,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
    </div>
    <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="form-group">
                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="show_signature" type="checkbox" {{ isset($show_signature) ? 'checked': ''}}><span> Show Signature</span>
                            </label>
                        </div>
                    </div>    
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
    
                        <div class="col-md-6 form-group">
                            {{ Form::hidden('signature',$signature)}}
                            <label>Signature Panel:</label> <a class="btn btn-danger btn-xs" id="clear-canvas"><i class="fa fa-eraser"></i></a>
                            <div id="signature-panel" class="signature-panel" data-name="signature" data-height="120" data-width="400"></div>
                        </div>
                        <div class="col-md-6 form-group">
    
                            <label>Current Signature:</label>
                            <div>
                                @if(strlen($signature)>0)
                                <img id="currentsignature" src="{{$signature}}"> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@overwrite


@section('scripts')
 @parent
<script src="{{ asset('vendor/jqsignature/js/jq-signature.min.js') }}" type="text/javascript"></script>
<script>
 
    
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
  $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
 
  var s = $('#signature-panel').jqSignature().on('jq.signature.changed',function() {
      $("input[name='signature']").val($(this).jqSignature('getDataURL'));
      $('.update').prop('disabled', false);
         $('.button-generate').prop('disabled', true);
  }); // Setup
  
  $('#clear-canvas').on('click',function() {
      $('#signature-panel').jqSignature('clearCanvas');
      $("input[name='signature']").val('');
      $("#currentsignature").attr('src','');
      $('.update').prop('disabled', false);
         $('.button-generate').prop('disabled', true);
  });
  
  $('.show-below-title').change(function() {
    if($(this).prop("checked") == true){
        $('.below-title-group').val('This is to hold in escrow until release of funds - acknowledgement is mandatory');
    } else {
        $('.below-title-group').val('');
    }
  });
  
})
</script>
    
@endsection