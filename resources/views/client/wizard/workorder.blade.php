@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  .typerequired{
    color: red;
  }
  .lastdayrequired{
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
   

<div id="top-wrapper" >
    <br>
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step col-xs-2">
                <a type="button" class="btn  btn-default btn-circle">1</a>
                <p><small>Job/Contract Information</small></p>
            </div>
            <div class="stepwizard-step col-xs-2"> 
                <a type="button" class="btn btn-default btn-circle">2</a>
                <p><small>Job/Contract Parties</small></p>
            </div>
            <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle">3</a>
                <p><small>Attachments</small></p>
            </div>
            <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-success btn-circle" >4</a>
                <p style="color: black"><small><strong>Document to Order</strong></small></p>
            </div>
            @if (Auth::user()->client->billing_type!='invoiced')
            <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >5</a>
                <p><small>Payment</small></p>
            </div>
            <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >6</a>
                <p><small>Confirmation</small></p>
            </div>
            @else
            <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >5</a>
                <p><small>Confirmation</small></p>
            </div>
            @endif
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h3>New Work Order  - Select Your Work Order Type
                    <div class="pull-right">
                        <a class="btn btn-danger " href="{{ route('wizard.attachments',[$job->id])}}"><i class="fa fa-chevron-left"></i> Back</a>
                        @if($wo)
                            <div class="pull-right">
                                {!! Form::open(['route' => ['wizard.payments',$job->id,$wo->id],'autocomplete' => 'off','id'=>'form_payment']) !!}
                                <button class="btn btn-success btn-payment" type="submit"> Next  <i class="fa fa-chevron-right"></i></button>
                                {!! Form::close() !!}
                            </div>
                        @else
                            <button class="btn btn-success btn-store" form="wo_form" type="submit"> Next <i class="fa fa-chevron-right"></i></button>
                        @endif
                    </div>
                </h3> 
            </div>       
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h5 class="page-header">
                   Please select the Work Order type you wish to order below.  If this is your first order on a new job and you are working for anyone other than the landowner it will require you to order a Notice to Owner.  If you need this notice to go out sooner, please select the Rush option (there will be an additional charge for rush orders).  The Due Date and Mailing Date are based on your Job Start Date or Last Day on the Job depending on the Work Order type being ordered.  Orders with due dates that are fast-approaching will be required to be a rush order in order to be mailed out on time.
                </h5> 
            </div>
        </div>
    </div>
</div>
{!! Form::open(['route' => ['wizard.workorder.store',$job->id],'autocomplete' => 'off','id'=>'wo_form','name'=>'wo_form']) !!}
{!! Form::hidden('status','open')!!}
{!! Form::hidden('job_id',$job->id)!!}
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
                            <div class="col-xs-12 form-group">
                                <label>Job Name:</label>
                                {{$job->name }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Work Order Type:</label>
                                @if($wo)
                                {!!  Form::select('type',$wo_types,old("type",$wo->type), ['class' => 'form-control noticetype','id'=>'work_order_type','required'=>'true','data-toggle'=>'tooltip','data-placement'=>'top','title'=>'What kind of document is needed?']) !!}

                                @else
                                @if(count($job->workorders)==0)
                                {!!  Form::select('type',$wo_types,old("type","notice-to-owner"), ['class' => 'form-control noticetype','id'=>'work_order_type','required'=>'true','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'What kind of document is needed?']) !!}

                                @else

                                {!!  Form::select('type',$wo_types,old("type"), ['class' => 'form-control noticetype','id'=>'work_order_type','required'=>'true','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'What kind of document is needed?']) !!}
                                @endif    
                                @endif
                                
                            </div>
                             
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>&nbsp;</label>
                                <div class="checkbox checkbox-slider--b-flat">
                                    <label>
                                        @if($wo)
                                            @if($wo->is_rush)
                                            <input checked="true" name="is_rush"  id="is_rush" type="checkbox"><span>Is Rush?</span>
                                            @else
                                            <input name="is_rush" type="checkbox" id="is_rush" ><span>Is Rush?</span>
                                            @endif
                                        @else
                                            <input name="is_rush" type="checkbox" id="is_rush" ><span>Is Rush?</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                         </div>
                        <div class="row jobs_last_day hidden">
                            <div class="col-xs-12 col-md-6 form-group">
                                <label>Last Day on Job:</label>
                                 @if($job->last_day)
                                <input disabled required="true" name="last_day"  value="{{ old("last_day", $job->last_day->format('m/d/Y') ) }}"  data-date-autoclose="true" class="form-control date-picker" data-toggle="tooltip" data-placement="top" title="">
                                @else
                                <input disabled required="true" name="last_day"  value="{{ old("last_day") }}"  data-date-autoclose="true" class="form-control date-picker" data-toggle="tooltip" data-placement="top" title="">
                                @endif

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
                                of {{$job->client->county }} State of Florida, does appoint SUNSHINE NOTICES, INC., 
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
                            <p>The below named authority, hereinafter referred to as PRINCIPAL, in the County of {{$job->client->county }} State of Florida, does appoint SUNSHINE NOTICES, INC., his/her POWER OF ATTORNEY. In principal’s name and for principal’s use and benefit, said Power of Attorney is authorized hereby to prepare and mail a Notice of Non Payment based on the information provided on the request form and previously furnished. Giving and granting to said Power of Attorney full power and authority to do everything necessary to be done relative to any of the foregoing as fully to all intents and purposes as the principal might or could do if personally present. Principal understands and accepts full responsibility for the final completion of any and all Notice of Non Payment he/she directs under this authorization and assumes full responsibility for the contents of this Notice of Non Payment.</p>
                        </div>
                    </div>
                </div>

                <div class="AdditionalQuestions">
                
                </div>
            </div>
        </div>
    </div>
</div>
    {!! Form::close() !!}
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
var job_started_at = '{{ $job->started_at }}';
var force_rush = false;
var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
$('.btn-payment').click(function(){
    //$('#form_payment').submit();
    $('.btn-payment').addClass("disabled");
    $('.btn-payment').css('pointer-events','none');
}); 
$('.btn-store').click(function(){
   
    if (M[1]!='Chrome' && M[1]!='Firefox' ){
        if ($("select[name='type']").val()=='claim-of-lien'){
            if($('.date-picker').val()=="" || $('.date-picker').val()==null)
            {
                
                $(".date-picker").parent().find('.lastdayrequired').remove();
                $(".date-picker").after("<p class='lastdayrequired'>This field is required.</p>");
            }
            else{
                submit_form();
            }
        } else{
            submit_form();
        }
    }
}); 
$("select[name='type']").click(function(){
    $("select[name='type']").parent().find('.typerequired').remove();
});
$(".date-picker").click(function(){
   $(".date-picker").parent().find('.lastdayrequired').remove();
});
function submit_form(){
    if (!$("select[name='type']").val()){ 
        $("select[name='type']").parent().find('.typerequired').remove();
        $("select[name='type']").after("<p class='typerequired'>This field is required.</p>");
        return;  };
    var k=0;
    $('.answer').each(function(e){
        var require=$(this).attr('required');
        var value=$(this).val().trim();
        if (require=='required' && value==""){
            $(this).parent().find('label').html('Answer: <span> This field is required.</span>');
            $(this).parent().find('label').find('span').css('color','red');

            k++;
        }

    });
    if (k==0){
        $('#wo_form').submit();
        $('.btn-store').addClass("disabled");
        $('.btn-store').css('pointer-events','none');
    }
}

$('.noticetype').click(function(){
    $('.btn-success').removeClass("disabled");
    $('.btn-success').css('pointer-events','auto');
});
$(function () {
  $('*').mousedown(function(){
    $('.answer').each(function(e){
        $(this).parent().find('label').html('Answer: ');
        
    });
  });
  $('[data-toggle="tooltip"]').tooltip();
 
  $('.date-picker').datepicker();
  $("select[name='type']").on('change',function() {
      $('.AdditionalQuestions').empty();
      $('.disclaimer').addClass('hidden');
      var type = $(this).val();
      $('div.jobs_last_day').addClass('hidden');
      $("input[name='last_day']").attr('disabled', true);
      if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
            if (type == 'claim-of-lien') {
                $('.disclaimer.claim-of-lien').removeClass('hidden');
            }
          
            $('div.jobs_last_day').removeClass('hidden');
            $("input[name='last_day']").attr('disabled', false);
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
      //alert('{{ route("notices.getfields")}}' + '/?work_order_type=' + work_order_type);
      $.get('{{ route("notices.getfields")}}' + '/?work_order_type=' + work_order_type, function(data) {
            if(data.length>0){
              var AdditionalQuestions='<div class="panel panel-default">                            <div class="panel-heading">                               Additional Questions                            </div>                            <div class="panel-body">                                <div class="row">                                  <table class="table lines">                                    </table>                                </div>                            </div>                        </div>';
              $('.AdditionalQuestions').html(AdditionalQuestions);
           } else{
              $('.AdditionalQuestions').empty();
           };
           var question_lists="";
           data.forEach(function(element){
            // question_lists+=
            // `<tr class="question-line-`+element.id+`"><td>
            //    <div class="col-xs-6 form-group">
            //        <label>Question:</label><br>
            //        <label name="question[`+element.id+`]"   class="  question noucase" data-toggle="tooltip" data-placement="top" title=""  > `+element.field_label+`</label>
            //    </div>
            //    <div class="col-xs-6 form-group">
            //        <label>Answer</label>`;

            //    var required=['',' required autofocus '];    
            //    if (element.field_type=='textbox')  {  
            //        question_lists+=`<input name="answer[`+element.id+`]" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" `+required[element.required]+`>`
            //    }
            //    if (element.field_type=='largetextbox') {   
            //        question_lists+=`<textarea name="answer[`+element.id+`]" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" `+required[element.required]+`></textarea>`
            //    }

            //    if (element.field_type=='date')  {  
            //       question_lists+= `<input name="answer[`+element.id+`]" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" `+required[element.required]+`>`
            //    }
            //    if (element.field_type=='dropdown') {   
            //       var drop_list=JSON.parse(element.dropdown_list);
            //       question_lists+=`<select name="answer[`+element.id+`]" class="form-control" `+required[element.required]+`><option value=""> </option>`;
            //         $.each(drop_list,function(key,value){
            //            question_lists+=`<option value="`+key+`">`+value+`</option>`;  
            //         });

            //       question_lists+=`</select>`;

            //    }

            //   question_lists+=`</div></td></tr>`;


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
           $('.date-picker').datepicker();
      });

  });
  $("select[name='type']").trigger('change');
  $("input[name='last_day']").on('change', function (){
        var type = $("select[name='type']").val();
        var last_day = $("input[name='last_day']").val();
        if (last_day.length > 0) {
            calculateDates(type);
        }else {

        }
        $('.btn-success').removeClass("disabled");
        $('.btn-success').css('pointer-events','auto');
  });
  
  $('form').submit(function() {
         $("input[name='is_rush']").attr('disabled', false); 
         return true;
  });
  
});

function calculateDates(type) {
      $("input[name='is_rush']").attr('checked', false); // Checks it
      var checkBox = document.getElementById("is_rush");checkBox.checked=false;
      $("input[name='due_at']").val('');
      $("input[name='mailing_at']").val('');
      
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
                 // if (dif >= 4) {
                 //      $("input[name='is_rush']").attr('checked', true); // Checks it
                 //      $("input[name='is_rush']").attr('disabled', true); 
                 //    } else {
                        $("input[name='is_rush']").attr('checked', false);
                        var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                        $("input[name='is_rush']").attr('disabled', false); 
                //}
                var due_at = moment().add(10,'days');
                var mailing_at = moment().add(7,'days');
            }
      }
      
      $("input[name='due_at']").val(due_at.format('MM/DD/YYYY'));
      $("input[name='mailing_at']").val(mailing_at.format('MM/DD/YYYY'));
  }

 
   
</script>
    
@endsection