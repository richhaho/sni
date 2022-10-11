@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
.select2{
  width: 100% !important
}
.requiredfiled{
  color: red;
}
.stepwizard-step p {
    margin-top: 0px;
    color:#666;
}
.stepwizard-row {
    display: table-row;
}
.stepwizard {
    display: table;
    width: 100%;
    position: relative;
    pointer-events:none;
}

.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-index: 0;
}
.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}
.btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
} 
@media screen and (max-width: 500px) {
    .stepwizard-step p {
        display: none !important;
    }
}
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    
      
     
    {!! Form::open(['route' => 'wizard2.setjobworkorder','autocomplete' => 'off']) !!}
        
         {{ Form::hidden('client_id', $client_id,['id' => 'client_id']) }}
        <?php
        $client = \App\Client::findOrFail($client_id);
        ?>
        @if ($contract_tracker)
        {{ Form::hidden('contract_tracker', $contract_tracker->id) }}
        @endif
        <div id="top-wrapper" >
          <br>
          <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step col-xs-3">
                    <a type="button" class="btn btn-success btn-circle">1</a>
                    <p style="color: black"><small><strong>Job/Contract Information & Workorder</strong></small></p>
                </div>
                <div class="stepwizard-step col-xs-3"> 
                    <a type="button" class="btn btn-default btn-circle">2</a>
                    <p><small>Job/Contract Parties & Attachments</small></p>
                </div>
                @if (Auth::user()->client->billing_type!='invoiced')
                <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >3</a>
                    <p><small>Payment</small></p>
                </div>
                <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >4</a>
                    <p><small>Confirmation</small></p>
                </div>
                @else
                <div class="stepwizard-step col-xs-3">    <a type="button" class="btn btn-default btn-circle" >3</a>
                    <p><small>Confirmation</small></p>
                </div>
                @endif
            </div>
          </div>
          <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header"> @if(count($jobs)>1 )
                    New Work Order - Job/Contract Information
                    @else
                    New Work Order - New Job/Contract Information
                    @endif
                    
                </h1>       
            </div>
          </div>
          <div class="container-fluid">
            <div class="col-xs-12">
              <h5>To begin the process of creating your work order we need to know what Job/Contract this is for.  Think of this as your contract with a particular person on a particular jobsite.  If it is a Job/Contract you have already entered, select it from the Job listing dropdown below.  If it is a new Job/Contract, select New Job and enter the information for the new job.</h5>

            </div>
          </div>
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
                      <p><strong>Lets get started...</strong></p>
                      <p><strong>What type of document do you wish to order?</strong></p>
                      <p><strong>Please Note: Your work order will not be created for processing until you reach the confirmation screen in this wizard.</strong></p>
                    </div>
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Work Order Type:
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                  <div class="col-xs-12 form-group">
                                  {!!  Form::select('wo_type',$wo_types,old("type",$wo ? $wo->type:''), ['class' => 'form-control noticetype','id'=>'work_order_type','required'=>'true','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'What kind of document is needed?']) !!}
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                      
                    </div>
                    <div class="col-xs-12 hidden" id="job-type-buttons-group">
                      <p><strong>Is this work order for a new job or a job you have previously entered in to our system?</strong></p>
                      <button class="btn btn-success btn-new-job" type="button"> New Job</button>
                      <button class="btn btn-success btn-existing-job" type="button"> Existing Job</button>
                      <button class="btn btn-success btn-from-notice" type="button"> I want to create new job from notice I received from Sunshine Notices</button>
                    </div>
                    <div class="col-xs-12">
                      <br>
                    </div>
                </div>
                <div class="row {{$job_id || count($errors) > 0 ? '':'hidden'}}" id="job-create-group">
                    @if(count($jobs)>0)
                    <div class="col-xs-12 select-existing-job hidden">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Select a Job  from the list below.
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group job_id_group">
                                    {!!  Form::select('job_id',$jobs,old("job_id",$job_id), ['class' => 'form-control','id'=>'job_id']) !!}
                                    @if (count($errors) > 0 && !old("job_id",$job_id))
                                      <input type="hidden" name="job_id" value="0">
                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @component('client.wizard2.dynamicforms.pulljobfromnotice')
                      @slot('job_number') 
                          {{ $job_number }}
                      @endslot
                      @slot('secret_key') 
                          {{ $secret_key }}
                      @endslot
                    @endcomponent
                    @else
                        {!! Form::hidden('job_id',0) !!}
                    @endif
                    {!! Form::hidden('pulled_job_id',0, ['class'=>'pulled_job_id']) !!}
                    <div class="col-xs-12 {{$job_id || count($errors) > 0 ? '':'hidden'}}" id="job-info-section">
                      <p class="pull_job_error text-danger hidden"> * Job does not exist.</p>
                      <div class="new_job_fields row job_info_group">
                        @if ($job_id == "" )
                            @include('client.wizard2.dynamicforms.jobformempty')
                        @else
                            @include('client.wizard2.dynamicforms.jobform',['job' => App\Job::FindOrFail($job_id)])
                        @endif    
                      </div>
                    </div>
                </div>

                <div class="row workorder_create workorder-create-group hidden">
                  <div class="col-xs-12">
                      <div class="panel panel-default">
                          <div class="panel-heading">
                            Workorder Details
                          </div>
                          <div class="panel-body">
                              <div class="row">
                                  <div class="col-xs-12 col-md-6 form-group">
                                      <label>&nbsp;</label>
                                      <div class="checkbox checkbox-slider--b-flat">
                                          <label>
                                              <input name="is_rush" type="checkbox" id="is_rush" ><span>Is Rush?</span>
                                          </label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">   
                                  <div class="col-xs-12 col-md-6 form-group">
                                      <label>Due Date:</label>
                                      @if($wo)
                                      <input readonly required="true" name="mailing_at"  value="{{ old("mailing_at", $wo->mailing_at->format('m/d/Y') ) }}" class="form-control" data-toggle="tooltip" data-placement="bottom" title="Must be mailed certified by this date in order to Not incur Rush/Express Mail Charges">
                                      @else
                                      <input readonly required="true" name="mailing_at"  value="{{ old("mailing_at") }}" class="form-control" data-toggle="tooltip" data-placement="bottom" title="Must be mailed certified by this date in order to Not incur Rush/Express Mail Charges">
                                      @endif
                                    
                                  </div>  
                                  <div class="col-xs-12 col-md-6 form-group">
                                      <label>Max. Mailing  Date:</label>
                                      @if($wo)
                                      <input readonly required="true" name="due_at"  value="{{ old("due_at", $wo->due_at->format('m/d/Y') ) }}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                      @else
                                      <input readonly required="true" name="due_at"  value="{{ old("due_at") }}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                      @endif
                                      <p>Will need to go Express mail by this date - If applicable click on Rush</p>
                                      
                                  </div>
                              </div>
                              <div class="col-xs-12 disclaimer claim-of-lien hidden">
                                  <p>WE HEREBY CERTIFY the previous described materials were used, 
                                      or labor and services were performed, in the improvement of 
                                      the real property named above; that we/I have not executed a 
                                      waiver or release of lien for the sums claimed. All of the 
                                      above stated information is true and correct to the best of 
                                      the undersigned's information and that a proper business 
                                      and professional license is held by the lienor for the 
                                      work performed and the amounts claimed due.</p>

                                  <p>The Above information will be used by Sunshine Notices, Inc. 
                                      to complete your Claim of Lien. We act solely as an administrative service. 
                                      Anyone seeking legal advice should contact an attorney. </p>

                                  <p>I understand that I hereby waive any claim against Sunshine Notices, Inc. due 
                                      to their inability to effectuate timely or proper service. Furthermore, 
                                      it is expressly understood that I waive any claim against Sunshine Notices, Inc. 
                                      due to errors created by the US Postal Service, courier service or the county 
                                      recorder. Two weeks are reasonably necessary for completion of a Claim of Lien. 
                                      A request for Claim of Lien received after the 80th day from the last day on the job, 
                                      will be subject to additional charges. These charges may include a rush 
                                      fee of up to $100.00 and any necessary courier fees or fees for overnight mail.
                                      Payment of $250.00 for the Claim of Lien is due at time of request 
                                      (this does not include rush fees). Any Claim Of Lien request canceled before 
                                      preparation will be subject to a $50.00 fee. Any Claim of Lien canceled after 
                                      preparation will be billed at the full rate. There is no discount or reduced 
                                      rate after the preparation of the Claim Of Lien. We reserve the right to 
                                      refuse or reject any Claim of Lien request. </p>

                                  <p class="text-center"><strong>CLAIM OF LIEN AUTHORIZATION</strong></p>

                                  <p>The below named authority, hereinafter referred to as PRINCIPAL, in the County 
                                      of {{$job ? $job->client->county : 'your county' }} State of Florida, does appoint SUNSHINE NOTICES, INC., 
                                      his/her POWER OF ATTORNEY. In principal’s name and for principal’s use and benefit, 
                                      said Power of Attorney is authorized hereby to prepare and record a Claim of Lien 
                                      based on the information provided on the request form and previously furnished.  
                                      Giving and granting to said Power of Attorney full power and authority to do 
                                      everything necessary to be done relative to any of the foregoing as fully to all 
                                      intents and purposes as the principal might or could do if personally present. 
                                      Principal understands and accepts full responsibility for the final completion 
                                      of any and all Claims of Lien he/she directs under this authorization and 
                                      assumes full responsibility for the contents of those Claims of Lien.</p> 
                              </div>
                              
                              <div class="col-xs-12 disclaimer notice-of-nonpayment-for hidden">
                                  <h4>NNP DISCLAIMER</h4>
                                  <p>WE HEREBY CERTIFY 1)the above described materials were used, or labor and services were performed in the improvement of the real property named above 2) A notice to owner was served within 45 days from my start date if I was not in direct contract with the general contractor, 3) We are a first or second position sub,  and 4) no waiver or release of lien for the sums claimed has been executed. All of the foregoing information is true and correct to the best of the undersigned's information and that a proper business and professional license is held by the lienor for the work performed and the amounts claimed due. </p>
                                  <p style="fone-weight:900">Furthermore, we understand and hereby certify that we have followed the rules of the Lien Law effective October 1, 2019 with respect to filing Notices Of Non Payment and have legal right to file this Notice of Non Payment as a first or second tier sub and that it is against the law to knowingly file if any of the above statements are incorrect.  We understand that any and all information provided will be used by Sunshine Notices, Inc. to complete my request for Notice of Non Payment solely as an administrative service provider and Sunshine Notices, Inc. has NOT provided any legal advice in connection with processing this request and we have been advised to seek legal advice with a licensed attorney should we need to do so. </p>
                                  <p>We understand and hereby waive any claim against Sunshine Notices, Inc. due to their inability to effectuate timely or proper service. Furthermore, it is expressly understood that I waive any claim against Sunshine Notices, Inc. due to errors created by the US Postal Service, courier service or the county recorder. Two weeks are reasonably necessary for completion of a Notice of Nonpayment. A request for Notice of Nonpayment received after the 75th day from the last day on the job, will be subject to additional charges, ie Rush Fees, Express Mail etc. Payment of $250.00 for the Notice of Nonpayment is due at time of request (this does not include rush fees). Any Notice of Nonpayment request canceled before preparation may be subject to a $50.00 fee.  We reserve the right to refuse or reject any Notice of Nonpayment request.</p>
                                  <br>
                                  <center><h4>NOTICE OF NON PAYMENT <span style="font-size:13px;font-weight:900">AUTHORIZATION</span></h4></center>
                                  <p>The below named authority, hereinafter referred to as PRINCIPAL, in the County of {{$job ? $job->client->county : 'your county' }} State of Florida, does appoint SUNSHINE NOTICES, INC., his/her POWER OF ATTORNEY. In principal’s name and for principal’s use and benefit, said Power of Attorney is authorized hereby to prepare and mail a Notice of Non Payment based on the information provided on the request form and previously furnished. Giving and granting to said Power of Attorney full power and authority to do everything necessary to be done relative to any of the foregoing as fully to all intents and purposes as the principal might or could do if personally present. Principal understands and accepts full responsibility for the final completion of any and all Notice of Non Payment he/she directs under this authorization and assumes full responsibility for the contents of this Notice of Non Payment.</p>
                              </div>
                          </div>
                      </div>

                      <div class="AdditionalQuestions">
                      
                      </div>
                  </div>
                </div>
                <div class="row workorder-create-group hidden">
                    <div class="col-xs-12 text-right job_info_group">
                        @if(session()->has('wizard2.newjob'))
                            @if(session('wizard2.newjob'))
                            <button class="btn btn-success next-button" type="submit">  <span>  Create Work Order </span> <i class="fa fa-chevron-right"></i></button>
                            @else
                            <button class="btn btn-success next-button" type="submit">  <span>  Create Work Order </span> <i class="fa fa-chevron-right"></i></button>
                            @endif
                        @else
                        <button class="btn btn-success next-button" type="submit">  <span>  Create Work Order </span> <i class="fa fa-chevron-right"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
<div class="modal fade" id="modal-existing-notice" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-left">Existing Unpaid Notice</h4>
      </div>
      <div class="modal-body text-left">
          <p>It looks like you started entering this order already.  Do you want to continue with your previous order or begin a new one?</p>
      </div>
      {!! Form::open(['route' => 'client.invoices.payment'])!!}
      <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Create New Work Order</button>&nbsp;&nbsp;
            <a class="btn btn-warning btn-continue-with-existing-notice" href=""> Continue with Previous Work Order</a> 
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
@if ($contract_tracker)
<script>
  var ctJobName = "{{$contract_tracker->name}}";
  var ctJobStartDate = "{{date('m/d/Y', strtotime($contract_tracker->start_date))}}";
</script>
@else 
<script>
  var ctJobName = null;
  var ctJobStartDate = null;
</script>
@endif
<script>
  var jobNumber = "{{$job_number}}";
  var secretKey = "{{$secret_key}}";
</script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.next-button').click(function(){
    $('.next-button').addClass("disabled");
    $('.next-button').css('pointer-events','none');
    setTimeout(() => {
      $('.next-button').removeClass("disabled");
      $('.next-button').css('pointer-events','auto');
    }, 1500);
}); 
  
  $('input').click(function(){
    
    $('.next-button').removeClass("disabled");
    $('.next-button').css('pointer-events','auto');
  });
  $('input').keydown(function(){
      $('.next-button').removeClass("disabled");
      $('.next-button').css('pointer-events','auto');
  });

  
var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 

$(function () {
  const getJobId = '{{$job_id}}';
  var isExistingJob = getJobId ? true : false;
  function checkExistingWorkorder() {
    const work_order_type = $('#work_order_type').val();
    let job_id = $('#job_id').val();
    if (!work_order_type || !job_id || !isExistingJob) return;
    $.get('{{url("/client/notices/existing-unpaid")}}?job_id='+job_id+'&work_type='+work_order_type, function( res ) {
      let toUrl = '';
      if (!res.work_id) return;
      if (res.invoice_id) {
        toUrl = '{{url("/client/wizard2/get-unpaid-notice")}}?invoice_id='+res.invoice_id+'&work_id='+res.work_id;
      } else {
        toUrl = '{{url("/client/wizard2/get-existing-notice")}}?job_id='+job_id+'&work_id='+res.work_id;
      }
      $('.btn-continue-with-existing-notice').attr('href', toUrl);
      $('#modal-existing-notice').modal('show');
    });
  }
  $('#work_order_type').change(function() {
    if ($('#work_order_type').val()) {
      if (ctJobName) {
        showJobCreateGroup('new-job');
      } else {
        $('#job-type-buttons-group').removeClass('hidden');
      }
    } else {
      $('#job-type-buttons-group').addClass('hidden');
    }
    checkExistingWorkorder();
  });
  $('.btn-new-job').click(function() {
    showJobCreateGroup('new-job');
  });
  $('.btn-existing-job').click(function() {
    showJobCreateGroup('existing-job');
  });
  $('.btn-from-notice').click(function() {
    showJobCreateGroup('from-notice');
  });
  function showJobCreateGroup(type) {
    isExistingJob = false;
    $('#job-create-group').removeClass('hidden');
    $('#job-info-section').addClass('hidden');
    $('.select-existing-job').addClass('hidden');
    $('.workorder-create-group').addClass('hidden');
    $('.job_id_group input[type="hidden"]').remove();
    if (type == 'new-job') {
      $('#job-info-section').removeClass('hidden');
      $('.job_id_group').append('<input type="hidden" name="job_id" value="0">');
      $('.workorder-create-group').addClass('hidden');
      $('.select-existing-job').addClass('hidden');
      $('.new_job_fields').load('{{ url ("/client/wizard2/job")}}' + '/0/form', function() {
          $('#job_type').trigger('change');
          $('.date-picker').datepicker();
          getInterestRate($('#client_id').val());
          getDefaultMaterials($('#client_id').val());
          var t = $("select[name='wo_type']").val();
          if (t == 'claim-of-lien' || t == 'notice-of-non-payment' || t == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || t == 'notice-of-nonpayment-for-government-jobs-statutes-255' || t == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
            $("input[name='last_day']").parent().find('label').addClass("requiredfiled");
          } else {
            $("input[name='last_day']").parent().find('label').removeClass("requiredfiled");
          }
          if (ctJobName) {
            $("input[name='name']").val(ctJobName);
          }
          if (ctJobStartDate) {
            $("input[name='started_at']").val(ctJobStartDate);
            changeStartedAt()
          }
      });
    } else if (type == 'existing-job') {
      isExistingJob = true;
      $('#job-info-section').removeClass('hidden');
      $('.select-existing-job').removeClass('hidden');
      $('.workorder-create-group').removeClass('hidden');
      jobIdChange();
    } else if (type == 'from-notice') {
        $('.select-existing-job').addClass('hidden');
        reset2NumbersField();
        $('#modal-pulljobfromnotice').modal('show');
    }
  }
  
  $('input[type="number"]').keydown( function(e){
      var rate=$(this).val();
       
      if ($(this).attr('name')=='interest_rate'){
        if (parseFloat(rate)>99.99 && e.keyCode!=8 && e.keyCode!=46 && e.keyCode!=37  && e.keyCode!=39){ e.preventDefault();return;}
        if (rate.length>5 && e.keyCode!=8 && e.keyCode!=46 && e.keyCode!=37 && e.keyCode!=39){
          e.preventDefault();return;
        }
      } else {
        if (parseFloat(rate)>999999999999.99 && e.keyCode!=8 && e.keyCode!=46 && e.keyCode!=37 && e.keyCode!=39){ e.preventDefault();return;}
        if (rate.length>12 && e.keyCode!=8 && e.keyCode!=46 && e.keyCode!=37 && e.keyCode!=39){
          e.preventDefault();return;
        }
      }
      if(e.keyCode>=48 && e.keyCode<=57){
        return;
      };
      if (e.keyCode==190 || e.keyCode==46 || e.keyCode==13 || e.keyCode==9 ){return;}
      if(e.keyCode>=96 && e.keyCode<=105){
        return;
      };
      if (e.keyCode==110){return;}
      if (e.keyCode==8 || e.keyCode==37 || e.keyCode==39 || e.keyCode==38 || e.keyCode==116){return;}

      e.preventDefault();
    });

  $('[data-toggle="tooltip"]').tooltip();
  
  if (M[1]=='Chrome' || M[1]=='Firefox' ){
    $('#job_id').select2();
  }
  
  $('.date-picker').datepicker();
 
  $('#job_id').on ('change', function () {
    jobIdChange();
    checkExistingWorkorder();
  });
  function jobIdChange() {
    $('.job_info_group').removeClass('hidden');
    $('.pull_job_error').addClass('hidden');
    var xid = $('#job_id').val();
    $('.new_job_fields').load('{{ url ("/client/wizard2/job")}}' + '/' + xid + '/form', function() {
        $('#job_type').trigger('change');
        $('.date-picker').datepicker();
        if (xid == 0) {
            getInterestRate($('#client_id').val());
            getDefaultMaterials($('#client_id').val());
        }
        var t = $("select[name='wo_type']").val();
        if (t == 'claim-of-lien' || t == 'notice-of-non-payment' || t == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || t == 'notice-of-nonpayment-for-government-jobs-statutes-255' || t == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
          $("input[name='last_day']").parent().find('label').addClass("requiredfiled");
        } else {
          $("input[name='last_day']").parent().find('label').removeClass("requiredfiled");
        }
    });
  }
  $('.btn-pull-job-from-notice').click(function () {
    $('.pull_job_status').addClass('hidden');
    const jobNumber = $('.job_number').val();
    const jobSecret = $('.job_secret').val();
    if (!jobNumber || !jobSecret) {
      $('.pull_job_status').text('* Please fill above 2 numbers.');
      $('.pull_job_status').removeClass('hidden');
      return;
    }
    $.get('{{url("/client/wizard/job/pullnotice")}}?number='+jobNumber+'&secret='+jobSecret, function( data ) {
        if (data==0) {
          $('.pulled_job_id').val('0');
          $('.pull_job_status').text('* No job found.');
          $('.pull_job_status').removeClass('hidden');
          $('.job_info_group').addClass('hidden');
          $('.pull_job_error').removeClass('hidden');
          return;
        } else {
          $('.pulled_job_id').val(data);
          $('#job-info-section').removeClass('hidden');
          $('.select-existing-job').addClass('hidden');
          $('.workorder-create-group').removeClass('hidden');
          $('#modal-pulljobfromnotice').modal('hide');
          $('.new_job_fields').load('{{ url ("/client/wizard2/job")}}' + '/' + data + '/form', function() {
            $('#job_type').trigger('change');
            $('.date-picker').datepicker();
          });
          $('.workorder-create-group').removeClass('hidden');
        }
    });
  });
  
  if (jobNumber && secretKey) {
    $.get('{{url("/client/wizard/job/pullnotice")}}?number='+jobNumber+'&secret='+secretKey, function( data ) {
        if (data!=0) {
          $('.pulled_job_id').val(data);
          $('#job-create-group').removeClass('hidden');
          $('#job-info-section').removeClass('hidden');
          $('.select-existing-job').addClass('hidden');
          $('.workorder-create-group').removeClass('hidden');
          $('.new_job_fields').load('{{ url ("/client/wizard2/job")}}' + '/' + data + '/form', function() {
            $('#job_type').trigger('change');
            $('.date-picker').datepicker();
          });
          $('.workorder-create-group').removeClass('hidden');
        }
    });
  }
  
  function reset2NumbersField() {
    $('.pulled_job_id').val('0');
    $('.job_number').val('');
    $('.job_secret').val('');
    $('.pull_job_status').addClass('hidden');
  }
  $('.job_number').click(function(){
    $('.pull_job_status').addClass('hidden');
  });
  $('.job_secret').click(function(){
    $('.pull_job_status').addClass('hidden');
  });
 
     $('.new_job_fields').on('change','select#job_type',function() {
         
       var xval = $(this).val();
       if (xval == 'public') {
            $('.pnumber-group').show(); 
       } else {
           $('.pnumber-group').hide(); 
           $('#project_number').val('');
           $('#private_type').trigger('change');
       }
      
      $('div[class*="job-"]').hide();
      $('div[class*="job-' + xval + '"]').show();
       
    });
    
     $('body').on('change','#private_type',function() {
           var xval = $(this).val();
        $('div[class*="ptype-"]').hide();
        $('div[class*="ptype-' + xval + '"]').show();
        
        if (xval == "residential") {
             $('#is_condo').trigger('change');
        } else {
             $('#is_mall_unit').trigger('change');
        }
     });
     
     
      $('body').on('change','#is_condo',function() {
           var xval = $(this).val();
           if (xval == "1") {
             $('.is_condo').show();
             
           } else {
             $('.is_condo').hide();
           }
     });
    
    
      $('body').on('change','#is_mall_unit',function() {
           var xval = $(this).val();
            $('div[class*="is_mall_unit_"]').hide();
            $('div[class*="is_mall_unit_' + xval + '"]').show();
           
     });
    
    
    $('#job_type').trigger('change');
 
 
 
  var countries = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  // url points to a json file that contains an array of country names, see
  // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
  //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
  prefetch:  { url: '{{ route('list.countries') }}' , cache: false }
});

    // passing in `null` for the `options` arguments will result in the default
    // options being used
    $('#countries').typeahead(null, {
      name: 'countries',
      source: countries
    });
    
    
    var states = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '{{ route('list.states') }}/%QUERY',
            prepare: function(settings) {
                if ($('#countries').val().length > 0) { 
                    return settings.url.replace('%QUERY',  $('#countries').val());
                } else { 
                    return settings.url.replace('%QUERY',  'none');              
                }
            },
            cache:false
        }
    });
    
     $('#states').typeahead(null, {
      name: 'states',
      source: states
    });
    
    $('#states').focus(function () {
        states.initialize(true);
    }); 
    
    // var counties = new Bloodhound({
    //     datumTokenizer: Bloodhound.tokenizers.whitespace,
    //     queryTokenizer: Bloodhound.tokenizers.whitespace,
    //     // url points to a json file that contains an array of country names, see
    //     // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
    //     //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
    //     prefetch:  { url: '{{ route('list.counties') }}' , cache: false }
    //   });
      
    // $('#counties').typeahead(null, {
    //   name: 'counties',
    //   source: counties
    // });
     

    if (M[1]=='Chrome' || M[1]=='Firefox' ){
   
       @if(!old("interest_rate"))
      getInterestRate($('#client_id').val());
      @endif
      @if(!old("default_materials"))
      getDefaultMaterials($('#client_id').val());
      @endif
    }else{
      
      if($('#interest_rate').val().trim()==""){
        getInterestRate($('#client_id').val());
      }
      if($('#default_materilas').val().trim()==""){
        getDefaultMaterials($('#client_id').val());
      }
    }
    
    $('#client_id').on('change',function() {
         getInterestRate($('#client_id').val());
         getDefaultMaterials($('#client_id').val());
    });
    $('.address_1').attr('autocomplete','off');
});

function getInterestRate(client_id) {
    $.get('{{url("/client")}}/' + client_id + '/interestrate', function( data ) {
         $( "#interest_rate" ).val( data );
    });
}

function getDefaultMaterials(client_id) {
    $.get('{{url("/client")}}/' + client_id + '/defaultmaterials', function( data ) {
         $( "#default_materilas" ).html( data );
    });
}


////////////////////// Address Search Part ////////////////
var property_addresses=[];
var county='';
var wait=false;$('.wait').css('display','none');
$('body').on('keydown','.address_1',function(e) {
  if(wait) {e.preventDefault();return;}
  var address_1=$('.address_1').val();
  if(e.keyCode==32 && address_1 && address_1.indexOf(' ')<0){
    county=$('.county').val().toUpperCase();
    if (counties.indexOf(county)<0) return;
    wait=true;$('.wait').css('display','block');
    setTimeout(function(){wait=false;$('.wait').css('display','none');},6000);
    $.get('{{route("client.jobs.getaddress")}}?county='+county+'&address_1='+address_1 , function( data ) {
      property_addresses=data;
      var kkk=0;
      data.forEach(function(item,index){
        setTimeout(function() {
            $('.address_1_list').append('<option value="'+item.property_address_full.toUpperCase()+'">'+item.property_address_1+'</option>'); 
        }, 1);
            // kkk++;
            // if (kkk==data.length) {
            //     setTimeout(function() {
            //     //$('.address_2').focus();$('.address_1').focus();
            //   }, 1);
            // }
      });
      wait=false;$('.wait').css('display','none'); $('.address_2').focus();$('.address_1').focus();
    }).fail(function() {
         wait=false;$('.wait').css('display','none');
    });
  }
});
$('body').on('input','.exist-job-address-1',function() {
  if ($('.exist-job-address-1').val() == $('.exist-job-address-1-origin').val()) {
    $('.exist-job-address-changed').addClass('hidden');
  } else {
    $('.exist-job-address-changed').removeClass('hidden'); 
  }
});
$('body').on('input','.address_1',function() {
  if($('.address_1').val().indexOf(' ')<0){
    $('.address_1_list').empty();
    //$('.address_1_list').attr('id','address_1_list_noinput');
    return;
  };
  //$('.state').val('');
  $('.zip').val('');
  $('.city').val('');
  $('.address_2').val('');
  $('.address_corner').val('');
  $('.folio_number').val('');
  $('.legal_description').val('');

  $('.owner_name').val('');
  $('.owner_address_1').val('');
  $('.owner_address_2').val('');
  $('.owner_city').val('');
  $('.owner_state').val('');
  $('.owner_zip').val('');

  property_addresses.forEach(function(item,index){
    if (item.property_address_full.trim().toUpperCase()==$('.address_1').val().trim().toUpperCase() && item.property_county.toUpperCase()==county){
      $('.state').val(item.property_state.toUpperCase());
      $('.zip').val(item.property_zip.toUpperCase());
      $('.city').val(item.property_city.toUpperCase());
      $('.address_1').val(item.property_address_1.toUpperCase());
      $('.address_2').val(item.property_address_2.toUpperCase());
      $('.folio_number').val(item.parcel_id.toUpperCase());
      $('.legal_description').val(item.short_legal.toUpperCase());

      $('.owner_name').val(item.owner_name.toUpperCase());
      $('.owner_address_1').val(item.owner_address_1.toUpperCase());
      $('.owner_address_2').val(item.owner_address_2.toUpperCase());
      $('.owner_city').val(item.owner_city.toUpperCase());
      $('.owner_state').val(item.owner_state.toUpperCase());
      $('.owner_zip').val(item.owner_zip.toUpperCase());
    }
  });

  if (navigator.appVersion.indexOf('Edge') > -1){
    $('input[name="name"]').focus();
    $('.address_1').focus();    
  }

});
function enteringCounty(){
    $('.folio_number').val('');
    $('.legal_description').val('');
    county=$('.county').val().toUpperCase();
    if(!county) {
      $('.address_fields').css('display','none');

      //$('.state').val('');
      $('.zip').val('');
      $('.city').val('');
      $('.address_1').val('');
      $('.address_2').val('');
      $('.address_corner').val('');
      return; 
    }
    $('.address_fields').css('display','block');

    $('.address_1_list').empty();
    //$('.address_1_list').attr('id','address_1_list_noinput');

}

$('body').on('input','.county',function() {
  if (navigator.appVersion.indexOf('Edge') > -1){
    
    $('input[name="name"]').focus();
    $('.county').focus();    
  }
});

function enterJobName(e=null) {
  if ($('.job-name').val()) {
    $('.job-required-field').removeClass('hidden');
  } else {
    $('.job-required-field').addClass('hidden');
  }
};

</script>

<script>
$(function () {
  $('*').mousedown(function(){
    $('.answer').each(function(e){
        $(this).parent().find('label').html('Answer: ');
        
    });
  });
  $('[data-toggle="tooltip"]').tooltip();
 
  $('.date-picker').datepicker();
  $("select[name='wo_type']").on('change',function() {
      $("input[name='last_day']").parent().find('label').removeClass("requiredfiled");
      $('.workorder-create-group').addClass('hidden');
      var type = $("select[name='wo_type']").val();
      
      if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
        $("input[name='last_day']").parent().find('label').addClass("requiredfiled");  
        if ($('input[name="last_day"]').val()) {
          calculateDates(type);
          $('.workorder-create-group').removeClass('hidden');
        }
      } else {
        if ($('input[name="started_at"]').val()) {
          calculateDates(type);
          $('.workorder-create-group').removeClass('hidden');
        }
      }
      
      $('.AdditionalQuestions').empty();
      $('.disclaimer').addClass('hidden');
      var type = $(this).val();
      $("workorder_create input[name='last_day']").attr('disabled', true);
      if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
            if (type == 'claim-of-lien') {
                $('.disclaimer.claim-of-lien').removeClass('hidden');
            }
            var last_day = $("input[name='last_day']").val();
            if (last_day.length > 0){
                calculateDates(type);
            }else {

            }
      } else {
            calculateDates(type);
      }
      if(type.indexOf('notice-of-nonpayment-for')>-1){
            $('.notice-of-nonpayment-for').removeClass('hidden');
      }


      $('table.lines').empty();
      var work_order_type=$('#work_order_type').val();
      $.get('{{ route("notices.getfields")}}' + '/?work_order_type=' + work_order_type + '&work_order_id={{$wo ? $wo->id:""}}', function(data) {
        if(data.answer) {
           var question_lists="";
           var questions=data.question;
           if(questions.length>0){
                var AdditionalQuestions='<div class="panel panel-default">                            <div class="panel-heading">                               Additional Questions                            </div>                            <div class="panel-body">                                <div class="row">                                  <table class="table lines">                                    </table>                                </div>                            </div>                        </div>';
                $('.AdditionalQuestions').html(AdditionalQuestions);
           } else{
              $('.AdditionalQuestions').empty();
           };
           questions.forEach(function(element){
            if (!data.answer[element.id]) data.answer[element.id]="";
            question_lists+=
            '<tr class="question-line-'+element.id+'"><td>               <div class="col-xs-6 form-group">     <label>Question:</label><br>                   <label name="question['+element.id+']"   class="  question noucase" data-toggle="tooltip" data-placement="top" title=""  > '+element.field_label+'</label>               </div>               <div class="col-xs-6 form-group">                   <label>Answer</label>';

               var required=['',' required autofocus '];    
               if (element.field_type=='textbox')  {  
                   question_lists+='<input name="answer['+element.id+']" value="'+data.answer[element.id]+'" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" '+required[element.required]+' onclick="clickinput();">'
               }
               if (element.field_type=='largetextbox') {   
                   question_lists+='<textarea name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" '+required[element.required]+' onclick="clickinput();">'+data.answer[element.id]+'</textarea>'
               }

               if (element.field_type=='date')  {  
                  question_lists+= '<input name="answer['+element.id+']"  value="'+data.answer[element.id]+'" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" '+required[element.required]+' onclick="clickinput();"  onchange="clickinput();">'
               }
               if (element.field_type=='dropdown') {   
                  
                  var drop_list=JSON.parse(element.dropdown_list);
                    
                    question_lists+='<select name="answer['+element.id+']" class="form-control" '+required[element.required]+' onclick="clickinput();"><option value=""> </option>';
                    $.each(drop_list,function(key,value){
                       if (data.answer[element.id]==key ){
                          question_lists+='<option value="'+key+'" selected="selected">'+value+'</option>';  
                        }else{
                          question_lists+='<option value="'+key+'">'+value+'</option>';  
                        }
                    });

                  question_lists+='</select>';

               }
              question_lists+='</div></td></tr>';
           });
           $('table.lines').append(question_lists);
        } else {
            if(data.length>0){
                var AdditionalQuestions='<div class="panel panel-default">                            <div class="panel-heading">                               Additional Questions                            </div>                            <div class="panel-body">                                <div class="row">                                  <table class="table lines">                                    </table>                                </div>                            </div>                        </div>';
                $('.AdditionalQuestions').html(AdditionalQuestions);
            } else{
                $('.AdditionalQuestions').empty();
            };
            var question_lists="";
            data.forEach(function(element){
              question_lists+=
              '<tr class="question-line-'+element.id+'"><td>               <div class="col-xs-12 col-md-6 form-group">                   <label>Question:</label><br>                   <label name="question['+element.id+']"   class="  question noucase" data-toggle="tooltip" data-placement="top" title=""  > '+element.field_label+'</label>               </div>               <div class="col-xs-12  col-md-6 form-group">                   <label>Answer</label>';
                var required=['',' required autofocus '];    
                if (element.field_type=='textbox')  {  
                    question_lists+='<input name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" '+required[element.required]+'>'
                }
                if (element.field_type=='largetextbox') {   
                    question_lists+='<textarea name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" '+required[element.required]+'></textarea>'
                }
                if (element.field_type=='date')  {  
                    question_lists+= '<input name="answer['+element.id+']" data-date-autoclose="true" class="form-control date-picker answer" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" '+required[element.required]+'>'
                }
                if (element.field_type=='dropdown') {   
                    var drop_list=JSON.parse(element.dropdown_list);
                    question_lists+='<select name="answer['+element.id+']" class="form-control answer" '+required[element.required]+'><option value=""> </option>';
                      $.each(drop_list,function(key,value){
                        question_lists+='<option value="'+key+'">'+value+'</option>';  
                      });
                    question_lists+='</select>';
                }
                question_lists+='</div></td></tr>';
            });
            $('table.lines').append(question_lists);
        }
        $('.date-picker').datepicker();
      });

  });
  $("select[name='wo_type']").trigger('change');
  
  $('form').submit(function() {
         $("input[name='is_rush']").attr('disabled', false); 
         return true;
  });
  
});

$('body').on('change','input[name="started_at"]',function() {
  changeStartedAt()
});
function changeStartedAt() {
  $('.workorder-create-group').addClass('hidden');
  var type = $("select[name='wo_type']").val();
  if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
    if ($('input[name="last_day"]').val()) {
      calculateDates(type);
      $('.workorder-create-group').removeClass('hidden');
    }
  } else {
    if ($('input[name="started_at"]').val()) {
      calculateDates(type);
      $('.workorder-create-group').removeClass('hidden');
    }
  }
}
$('body').on('change','input[name="last_day"]',function() {
    $('.workorder-create-group').addClass('hidden');
    var type = $("select[name='wo_type']").val();
    if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
      if ($('input[name="last_day"]').val()) {
        calculateDates(type);
        $('.workorder-create-group').removeClass('hidden');
      }
    } else {
      if ($('input[name="started_at"]').val()) {
        calculateDates(type);
        $('.workorder-create-group').removeClass('hidden');
      }
    }
});
function calculateDates(type) {
      $("input[name='is_rush']").attr('checked', false); // Checks it
      var checkBox = document.getElementById("is_rush");checkBox.checked=false;
      $("input[name='due_at']").val('');
      $("input[name='mailing_at']").val('');
      var job_started_at = $("input[name='started_at']").val();
      job_started_at = job_started_at ? job_started_at : new Date()
      var stdt = moment(job_started_at);
      var today = moment();
      var last_day = $("input[name='last_day']").val();
    
      if (type=='notice-to-owner' || type== 'amended-notice-to-owner' ) {

           var dif = today.diff(stdt, 'days')
           if (dif >= 36 && dif <= 44) {
                $("input[name='is_rush']").attr('checked', true); // Checks it
                var checkBox = document.getElementById("is_rush");checkBox.checked=true;
                $("input[name='is_rush']").attr('disabled', true); 
                 
           } else {
                $("input[name='is_rush']").attr('checked', false);
                var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                $("input[name='is_rush']").attr('disabled', false);
                 
           }
           var due_at = moment(job_started_at).add(43,'days');
           var mailing_at = moment(job_started_at).add(39,'days');
           
      } else {
           if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                if (last_day.length > 0) {
                    var stdt = moment(last_day,'MM/DD/YYYY');
                    var dif = today.diff(stdt, 'days', true)
                    if (dif < 90 && dif>=76) {
                         $("input[name='is_rush']").attr('checked', true); // Checks it
                         var checkBox = document.getElementById("is_rush");checkBox.checked=true;
                         $("input[name='is_rush']").attr('disabled', true); 
                    } else {
                        $("input[name='is_rush']").attr('checked', false);
                        var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                        $("input[name='is_rush']").attr('disabled', false); 
                    }

                    var due_at = moment(last_day).add(89,'days');
                    var mailing_at = moment(last_day).add(89,'days');
                 } else {

                 }   
            } else {
                var dif = today.diff(stdt, 'days')
                    $("input[name='is_rush']").attr('checked', false);
                    var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                    $("input[name='is_rush']").attr('disabled', false); 
                var due_at = moment().add(10,'days');
                var mailing_at = moment().add(7,'days');
            }
      }
      $("input[name='due_at']").val(due_at ? due_at.format('MM/DD/YYYY') : '');
      $("input[name='mailing_at']").val(mailing_at ? mailing_at.format('MM/DD/YYYY') : '');
  }
</script>
@endsection