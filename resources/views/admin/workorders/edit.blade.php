@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .tab-pane {
        margin-top: 10px;
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
                <h1 class="page-header">Edit Work Order {{ $work->number }} - {{$work->service =='self' ? 'Self' : 'Full'}} Service<br>
                    <small style="font-size:12px;"> Created: {{ $work->created_at->format('m-d-Y h:i:s a')}}</small>
                    <div class="pull-right">
                        <a class="btn btn-danger " href="{{ Session::get('backUrl')}}"><i class="fa fa-times-circle"></i> Exit</a> &nbsp;&nbsp;
                        <a href="{{ route('jobs.summary',$work->job->id)}}" class="btn btn-primary"><i class="fa fa-book"></i> View Job Summary</a>&nbsp;&nbsp;
                        <a href="{{ route('workorders.sendlink',$work->id)}}" class="btn btn-warning"><i class="fa fa-send"></i> Send Link to Client</a>&nbsp;&nbsp;
                    </div>
                </h1>       
            </div>
            </div>
        </div>
            <div id="page-wrapper">
            
            <div class="container-fluid">
                <div class="btn-group">
                     
                  
                  <a class="btn btn-default " href="{{ route('workorders.newinvoice',$work->id)}}?fromindex=0"><i class="fa fa-file-text-o"></i> Create Invoice</a>
                        
                        @if(in_array($work->type,$available_notices))
                            @if(count($work->attachments->where('type','generated')) > 0)
                            @if (Session::get('user_role')=='admin')
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                            <a class="btn btn-default" href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                            @endif
                            <a class="btn btn-default" href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                           
                            @else
                           
                           <a class="btn btn-default " href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                            @if (Session::get('user_role')=='admin')
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-trash-o"></i> Delete PDF</a>
                            @endif
                            <a class="btn btn-default disabled" href=""><i class="fa  fa-eye"></i> View PDF</a>
                            
                            @endif

                        
                        @endif
                  
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
                
  
              
                 <div class="row">
                   
                 @if (Session::has('message'))
                     <div class="col-xs-12 message-box">
                     <div class="alert alert-info">{{ Session::get('message') }}</div>
                     </div>
                 @endif
                   @php 
                        $job = $work->job
                    @endphp
                  <div class="col-xs-12">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" id="wo_tabs" data-tabs="tabs">
                          <li role="presentation" class="active"><a href="#workorderinfo" aria-controls="profile" role="tab" data-toggle="tab">Work Order Info</a></li> 
                          <li role="presentation"><a href="#job" aria-controls="profile" role="tab" data-toggle="tab">Job Info</a></li>
                          <li role="presentation"><a href="#parties" aria-controls="messages" role="tab" data-toggle="tab">Job Parties</a></li>
                          <li role="presentation"><a href="#notes" aria-controls="messages" role="tab" data-toggle="tab">Notes</a></li>
                          <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments</a></li>
                          <li role="presentation"><a href="#invoices" aria-controls="invoices" role="tab" data-toggle="tab">Invoices</a></li>
                          @if ($work->service=='self')
                          <li role="presentation"><a href="#todos" aria-controls="todos" role="tab" data-toggle="tab">ToDos</a></li>
                          @endif
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane  active" id="workorderinfo">
                              @include('admin.workorders.components.orderinfo-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="attachments">
                              @include('admin.workorders.components.attachments-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane " id="job">
                              @include('admin.workorders.components.jobinfo-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="parties">
                                @include('admin.workorders.components.jobparties-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="notes">
                               @include('admin.notes.index', ['notes' => $work->notes,'e_name' => 'workorders','e_id' => $work->id])
                            </div>
                            <div role="tabpanel" class="tab-pane" id="invoices">
                                @include('admin.workorders.components.invoices-tab')
                            </div>
                            @if ($work->service=='self')
                            <div role="tabpanel" class="tab-pane" id="todos">
                               @include('admin.workorders.components.todos-tab', ['todos' => $work->todos(), 'word_id' => $work->id])
                            </div>
                            @endif
                        </div>


                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                   
                        </div>
                  </div>
                
            </div>
            <!-- /.container-fluid -->
            
        </div>
    
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-success').click(function(){
    $('.btn-success').addClass("disabled");
    $('.btn-success').css('pointer-events','none');
});

$('input').click(function(){
    $('.btn-success').removeClass("disabled");
    $('.btn-success').css('pointer-events','auto');
});
$('input').change(function(){
    $('.btn-success').removeClass("disabled");
    $('.btn-success').css('pointer-events','auto');
});
function clickinput(){
   $('.btn-success').removeClass("disabled");
    $('.btn-success').css('pointer-events','auto');
}

$(function () {
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $( '.uploadfile').submit( function(event){
        $(".filegroup p").remove();        
        var fe=$('input:file')[0].files[0].size;
        var max_uploadfileSize={{$max_uploadfileSize}};
        
        var file_name=$('input:file')[0].files[0].name;
        var ext=file_name.split('.').pop().toLowerCase();
        var ext_area=['pdf','jpeg','jpg','tiff','tif','doc','xls','docx','xlsx'];
        if (ext_area.indexOf(ext)==-1){
            $(".filegroup").append('<p>This file type is not permitted for upload.</p>');
            event.preventDefault();
        }

        if (fe>max_uploadfileSize){
            $(".filegroup").append('<p>This file is too large to upload.</p>');
            event.preventDefault();
        }
        
    });
    $('input:file').click( function(){
      $(".filegroup p").remove(); 
      $('.btn-success').removeClass("disabled");
      $('.btn-success').css('pointer-events','auto'); 
    });

    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    $('[data-toggle="tooltip"]').tooltip()
    $('.date-picker').datepicker();
    $(":file").filestyle();

    $('.custom_message_group').css('display', 'none');
    $("input[name='notify']").change(function(){
        if ($("input[name='notify']").prop('checked')) {
          $('.custom_message_group').css('display', 'block');
        } else {
          $('.custom_message_group').css('display', 'none');
        }
    });
    
    $('#wo_tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      })
      
    var hash = window.location.hash;
    //console.log(hash);
    if (hash.length > 0 ) {
       
            $('#wo_tabs a[href="' + hash + '"]').tab('show')
       
         $('#page-wrapper').scrollTop($(hash).offset().top);
    }
    
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).prev('tr').find("i.fa-plus-circle:last").removeClass("fa-plus-circle").addClass("fa-minus-circle");
    }).on('hidden.bs.collapse', function(){
        $(this).prev('tr').find(".fa-minus-circle:last").removeClass("fa-minus-circle").addClass("fa-plus-circle");
    });
 change_type();
    $('#work_order_type').change(function(){
        change_type();

    });
    function change_type(){
        $('table.lines').empty();
        $('.AdditionalQuestions').empty();
      var work_order_type=$('#work_order_type').val();
      $.get('{{ route("workorders.getfields")}}' + '/?work_order_id={{$work->id}}&work_order_type=' + work_order_type, function(data) {

           var question_lists="";
           var questions=data.question;
           if(questions.length>0){
              var AdditionalQuestions='<center><h3>Additional Questions</h3></center>';
              $('.AdditionalQuestions').append(AdditionalQuestions);
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
           $('.date-picker').datepicker();
      });
       
      calculateDates(work_order_type);
    }
    $('#job_id').on('change', function () {
      var job_id = $(this).val();
      getJobUsers(job_id);
    });
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

var job_started_at = '{{ $work->job->started_at }}';
var last_day = '{{ $work->job->last_day }}';;
function calculateDates(type) {
      //$("input[name='is_rush']").attr('checked', false); // Checks it
      //var checkBox = document.getElementById("is_rush");checkBox.checked=false;
      $("input[name='due_at']").val('');
      $("input[name='mailing_at']").val('');
      
      var stdt = moment(job_started_at);
      var today = moment();
    
      if (type=='notice-to-owner' || type== 'amended-notice-to-owner') {

           var dif = today.diff(stdt, 'days')
           if (dif >= 36) {
                // $("input[name='is_rush']").attr('checked', true); // Checks it
                // var checkBox = document.getElementById("is_rush");checkBox.checked=true;
                // $("input[name='is_rush']").attr('disabled', true); 
           } else {
                // $("input[name='is_rush']").attr('checked', false);
                // var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                // $("input[name='is_rush']").attr('disabled', false); 
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
                         // var checkBox = document.getElementById("is_rush");checkBox.checked=true;
                         // $("input[name='is_rush']").attr('disabled', true); 
                    } else {
                        // $("input[name='is_rush']").attr('checked', false);
                        // var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                        // $("input[name='is_rush']").attr('disabled', false); 
                    }

                    var due_at = moment(last_day).add(89,'days');
                    var mailing_at = moment(last_day).add(89,'days');
                 } else {

                 }   
            } else {
                var dif = today.diff(stdt, 'days')
                  
                        // $("input[name='is_rush']").attr('checked', false);
                        // var checkBox = document.getElementById("is_rush");checkBox.checked=false;
                        // $("input[name='is_rush']").attr('disabled', false); 
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