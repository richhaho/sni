@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('admin.navigation')
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
                                @if ($job_name && $job_id)
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::hidden('job_id',$job_id, ['class' => 'form-control','id'=>'job_id']) !!}
                                        {{ Form::text('job_name', $job_name,['class'=>'form-control','disabled' => true,'id'=>'job_name']) }}
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
                                @else
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Name Search:</label>
                                        <div class="input-group custom-search-form">
                                            <input id="job_search" type="text" class="form-control" placeholder="Search...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="job_search_button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::select('job_id',$jobs_list,old("job_id",$job_id), ['class' => 'form-control','id'=>'job_id', 'required'=>true]) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Number:</label>
                                        {{ Form::text('job_number','',['class'=>'form-control','disabled' => true,'id'=>'job_number']) }}
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Contract Amount:</label>
                                       
                                        {{ Form::text('job_contract_amount','',['class'=>'form-control','disabled' => true,'id'=>'job_contract_amount']) }}
                                    </div>
                                </div>
                                @endif
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
                                        <label>Max. Mailing Date:</label>
                                
                                        <input  required="true" name="due_at"  value="{{ old("due_at") }}" class="form-control date-picker"  data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Responsible User:</label>
                                        {!!  Form::select('responsible_user',[],'', ['class' => 'form-control','id'=>'responsible_user']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Manager:</label>
                                        {!!  Form::select('manager',$admin_users,'', ['class' => 'form-control','id'=>'manager']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Researcher:</label>
                                        {!!  Form::select('researcher',$admin_users,'', ['class' => 'form-control','id'=>'researcher']) !!}
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="AdditionalQuestions">
                        
                        </div>
                    </div>
                    
                    
        
                </div>
               
            </div>
            <!-- /.container-fluid -->
     
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
    $('.btn-save').css('pointer-events','none');
    setTimeout(() => {
        answer_click();
    }, 2000);
});
$('input').mousedown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});
$('input').keydown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});
$('.answer').keydown(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});
$('.answer').change(function () {
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
});
function answer_click(){
  $('.btn-save').removeClass("disabled");
  $('.btn-save').css('pointer-events','auto');
}

var job_started_at;
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
 
  $('.date-picker').datepicker();
 
  $('#job_id').on('change', function () {
      $('.btn-save').removeClass("disabled");
      $('.btn-save').css('pointer-events','auto');
      var job_id = $(this).val();
      getJobNumber(job_id);
      getContractAmount(job_id);
      getStartedAt(job_id);
      getLastDay(job_id);
      getJobUsers(job_id);
      setTimeout(() => {
        change_type();          
      }, 1000);
  });

  var job_id = $('#job_id').val();
  if (job_id) {
      getJobNumber(job_id);
      getContractAmount(job_id);
      getStartedAt(job_id);
      getLastDay(job_id);
      getJobUsers(job_id);
  } else {
      $('#job_id').select2();
  }
  $('#job_search_button').click(function() {
      var search_text = $('#job_search').val();
      if (!search_text) return;
      $.get('{{url("/admin/jobs")}}/search_jobs?search=' + search_text , function( data ) {
        $('#job_id').empty();
        $('#job_id').append('<option value=""> </option>');
        data.forEach(item => {
            var jobs = '<option value="'+item.id +'">'+item.name+'</option>';
            $('#job_id').append(jobs);
        });
        $('#job_id').select2();
      });
  });
  setTimeout(() => {
        change_type();
  }, 2000);
  $("select[name='type']").on('change',function() {
    change_type();
  });
  function change_type() { console.log($("input[name='last_day']").val())
      $('.AdditionalQuestions').empty();
      var type = $('#work_order_type').val();
      
      $.get('{{ url('admin/jobs/')}}' + '/' + job_id + '/type/' + type, function(data) {
         if(data=="YES") {
             $('#existentType').modal('show')
         } 
      });
      
      $('div.jobs_last_day').addClass('hidden');
      $("input[name='last_day']").attr('disabled', true);
      if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
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


      $('table.lines').empty();
      var work_order_type=$('#work_order_type').val();
      $.get('{{ route("workorders.getfields")}}' + '/?work_order_type=' + work_order_type, function(data) {
           if(data.length>0){
              var AdditionalQuestions='<div class="panel panel-default">                            <div class="panel-heading">                               Additional Questions                            </div>                            <div class="panel-body">                                <div class="row">                                  <table class="table lines">                                    </table>                                </div>                            </div>                        </div>';
              $('.AdditionalQuestions').append(AdditionalQuestions);
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
            //        question_lists+=`<input name="answer[`+element.id+`]" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" `+required[element.required]+` onclick="answer_click()">`
            //    }
            //    if (element.field_type=='largetextbox') {   
            //        question_lists+=`<textarea name="answer[`+element.id+`]" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" `+required[element.required]+` onclick="answer_click()"></textarea>`
            //    }

            //    if (element.field_type=='date')  {  
            //       question_lists+= `<input name="answer[`+element.id+`]" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" `+required[element.required]+` onclick="answer_click()">`
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
            '<tr class="question-line-'+element.id+'"><td>               <div class="col-xs-6 form-group">                   <label>Question:</label><br>                   <label name="question['+element.id+']"   class="  question noucase" data-toggle="tooltip" data-placement="top" title=""  > '+element.field_label+'</label>               </div>               <div class="col-xs-6 form-group">                   <label>Answer</label>';

               var required=['',' required autofocus '];    
               if (element.field_type=='textbox')  {  
                   question_lists+='<input name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" '+required[element.required]+' onclick="answer_click()">'
               }
               if (element.field_type=='largetextbox') {   
                   question_lists+='<textarea name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" '+required[element.required]+' onclick="answer_click()"></textarea>'
               }

               if (element.field_type=='date')  {  
                  question_lists+= '<input name="answer['+element.id+']" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" '+required[element.required]+' onclick="answer_click()">'
               }
               if (element.field_type=='dropdown') {   
                  var drop_list=JSON.parse(element.dropdown_list);
                  question_lists+='<select name="answer['+element.id+']" class="form-control" '+required[element.required]+'><option value=""> </option>';
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
    }
  // $('#work_order_type').change(function(){

  // });
  
  $("input[name='last_day']").on('change', function (){
        var type = $("select[name='type']").val();
        var last_day = $("input[name='last_day']").val()
        if (last_day.length > 0) {
            calculateDates(type)
        }else {

        }
        $('.btn-save').removeClass("disabled");
        $('.btn-save').css('pointer-events','auto');
  });    
   
//   var job_id = $('#job_id').val();
//   $.get('{{url("/admin/jobs")}}/' + job_id + '/startedat', function( data ) {
//         job_started_at =  data ;
//         $("select[name='type']").trigger('change');   
//   });
   
      
  function getJobNumber(job_id) {
    $.get('{{url("/admin/jobs")}}/' + job_id + '/number', function( data ) {
        $( "#job_number" ).val( data );
    });
   }
   
  function getContractAmount(job_id) {
    $.get('{{url("/admin/jobs")}}/' + job_id + '/contractamount', function( data ) {
        $( "#job_contract_amount" ).val( data );
    });
  }
   
  function getStartedAt(job_id) {
    $.get('{{url("/admin/jobs")}}/' + job_id + '/startedat', function( data ) {
        job_started_at =  data ;
    });
  }
   
  function getLastDay(job_id) {
    $( "#last_day" ).val('');
    $.get('{{url("/admin/jobs")}}/' + job_id + '/lastday', function( data ) {
        $( "#last_day" ).val( data );
    });
  }

  function getJobUsers(job_id) {
    $( "#responsible_user" ).empty();
    $.get('{{url("/admin/jobs")}}/' + job_id + '/users', function( data ) {
        let options = '<option value=""></option>';
        for (i=0; i<data.length; i++) {
          options += '<option value="' + data[i].id + '">' + data[i].first_name + ' ' + data[i].last_name + '</option>';
        }
        $( "#responsible_user" ).append(options);
    });  
  }

});

function calculateDates(type) {
      //$("input[name='is_rush']").attr('checked', false); // Checks it
      $("input[name='due_at']").val('');
      $("input[name='mailing_at']").val('');
      
      var stdt = moment(job_started_at);
      var today = moment();
      var last_day = $("input[name='last_day']").val();
      
      
      
      
      
      if (type=='notice-to-owner' ) {

           var dif = today.diff(stdt, 'days')
           if (dif >= 36) {
                // $("input[name='is_rush']").attr('checked', true); // Checks it
                // $("input[name='is_rush']").attr('disabled', true); 
           }
           var due_at = moment(job_started_at).add(43,'days');
           var mailing_at = moment(job_started_at).add(39,'days');
           
      } else {
           if (type == 'claim-of-lien' || type == 'notice-of-non-payment' || type == 'notice-of-nonpayment-for-bonded-private-jobs-statutes-713' || type == 'notice-of-nonpayment-for-government-jobs-statutes-255' || type == 'notice-of-nonpayment-with-intent-to-lien-andor-foreclose') {
                if (last_day.length > 0) {
                     var stdt = moment(last_day,'MM/DD/YYYY');
                     var dif = today.diff(stdt, 'days')
                    if (dif >= 86) {
                         // $("input[name='is_rush']").attr('checked', true); // Checks it
                         // $("input[name='is_rush']").attr('disabled', true); 
                    }
                    var due_at = moment(last_day).add(89,'days');
                    var mailing_at = moment(last_day).add(89,'days');
                 } else {

                 }   
            } else {
                var dif = today.diff(stdt, 'days')
                 if (dif >= 4) {
                      // $("input[name='is_rush']").attr('checked', true); // Checks it
                      // $("input[name='is_rush']").attr('disabled', true); 
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
