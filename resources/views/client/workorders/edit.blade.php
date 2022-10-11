@extends('client.layouts.app')

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
    @include('client.navigation')
@endsection

@section('content')
    
        
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Work Order {{ $work->number }} - {{$work->service =='self' ? 'Self' : 'Full'}} Service<br>
                    <small style="font-size:12px;"> Created: {{ $work->created_at->format('m-d-Y h:i:s a')}}</small>
                    <div class="pull-right">
                    @if ($work->service =='self')
                        @if (!in_array($work->status,['cancelled','cancelled charge','completed','cancelled no charge']) && $work->job->status != 'closed')
                            <a class="btn btn-warning" href="{{ route('client.notices.requestService',$work->id)}}"> Request Additional Services</a>
                            <a class="btn btn-primary" href="{{ route('client.research.start', $work->job->id)}}"> Start Research Wizard</a>
                            @if(count($work->attachments->where('type', 'generated'))>0)
                            <a class="btn btn-success" href="{{ route('client.notices.deletedocument',$work->id)}}"> Delete Generated Document(s)</a>
                            @else
                            <a class="btn btn-success" href="{{ route('client.notices.document',$work->id)}}"> Generate Document(s)</a>
                            @endif
                            <a href="{{ route('client.jobs.summary',$work->job->id)}}" class="btn btn-primary"><i class="fa fa-book"></i> View Job Summary</a>&nbsp;&nbsp;
                        @endif
                    @endif
                        <a class="btn btn-danger " href="{{ route('client.notices.index')}}"><i class="fa fa-times"></i> Exit</a>
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
                          <li role="presentation" class="active"><a href="#noticeinfo" aria-controls="profile" role="tab" data-toggle="tab">Work Order Info</a></li>
                          <li role="presentation" ><a href="#job" aria-controls="profile" role="tab" data-toggle="tab">Job Info</a></li>
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
                            <div role="tabpanel" class="tab-pane  active" id="noticeinfo">
                              @include('client.workorders.noticeform')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="attachments">
                              @include('client.workorders.components.attachments-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="job">
                              @include('client.workorders.components.jobinfo-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="parties">
                                @include('client.workorders.components.jobparties-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="notes">
                               @include('client.notes.index', ['notes' => $work->notes()->forClients()->get(),'e_name' => 'workorders','e_id' => $work->id])
                            </div>
                            <div role="tabpanel" class="tab-pane" id="invoices">
                                @include('client.workorders.components.invoices-tab')
                            </div>
                            @if ($work->service=='self')
                            <div role="tabpanel" class="tab-pane" id="todos">
                               @include('client.workorders.components.todos-tab', ['todos' => $work->todos(), 'word_id' => $work->id])
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
    $(this).addClass("disabled");
    $(this).css('pointer-events','none');
});
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


    $('form').on('submit',function() {
        
        $("input").prop( "disabled", false );
        $("select").prop( "disabled", false );
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


    $('#work_order_type').change(function(){
        change_type();

    });change_type();
    function change_type(){
        $('table.lines').empty();
        $('.AdditionalQuestions').empty();
        $('.saveButton').empty();
      var work_order_type=$('#work_order_type').val();
      $.get('{{ route("notices.getfields")}}' + '/?work_order_id={{$work->id}}&work_order_type=' + work_order_type, function(data) {
        
           var question_lists="";
           var questions=data.question;
           if(questions.length>0){
              var AdditionalQuestions='<div class="panel panel-default">                            <div class="panel-heading">                               Additional Questions                            </div>                            <div class="panel-body">                                <div class="row">                                  <table class="table lines">                                    </table>                                </div>                            </div>                        </div>';
              $('.AdditionalQuestions').append(AdditionalQuestions);
              $('.saveButton').append('<button class="btn btn-success " type="submit" form="edit_form"> <i class="fa fa-floppy-o"></i> Save</button>');

           } else{
              $('.AdditionalQuestions').empty();
              $('.saveButton').empty();
           };
           questions.forEach(function(element){
            if (!data.answer[element.id]) data.answer[element.id]="";
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
            //        question_lists+=`<input name="answer[`+element.id+`]" value="`+data.answer[element.id]+`" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" `+required[element.required]+`>`
            //    }
            //    if (element.field_type=='largetextbox') {   
            //        question_lists+=`<textarea name="answer[`+element.id+`]" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" `+required[element.required]+`>`+data.answer[element.id]+`</textarea>`
            //    }

            //    if (element.field_type=='date')  {  
            //       question_lists+= `<input name="answer[`+element.id+`]"  value="`+data.answer[element.id]+`" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" `+required[element.required]+`>`
            //    }
            //    if (element.field_type=='dropdown') {   
                  
            //       var drop_list=JSON.parse(element.dropdown_list);
                    
            //         question_lists+=`<select name="answer[`+element.id+`]" class="form-control" `+required[element.required]+`><option value=""> </option>`;
            //         $.each(drop_list,function(key,value){
            //            if (data.answer[element.id]==key ){
            //               question_lists+=`<option value="`+key+`" selected="selected">`+value+`</option>`;  
            //             }else{
            //               question_lists+=`<option value="`+key+`">`+value+`</option>`;  
            //             }
            //         });

            //       question_lists+=`</select>`;

            //    }

            //   question_lists+=`</div>
                
            //   </td></tr>`;


            question_lists+=
            '<tr class="question-line-'+element.id+'"><td>               <div class="col-xs-6 form-group">                   <label>Question:</label><br>                   <label name="question['+element.id+']"   class="  question noucase" data-toggle="tooltip" data-placement="top" title=""  > '+element.field_label+'</label>               </div>               <div class="col-xs-6 form-group">                   <label>Answer</label>';

               var required=['',' required autofocus '];    
               if (element.field_type=='textbox')  {  
                   question_lists+='<input name="answer['+element.id+']" value="'+data.answer[element.id]+'" class="form-control answer noucase" data-toggle="tooltip" data-placement="top" title="" '+required[element.required]+'>'
               }
               if (element.field_type=='largetextbox') {   
                   question_lists+='<textarea name="answer['+element.id+']" class="form-control answer noucase" data-toggle="tooltip" value="" data-placement="top" title="" '+required[element.required]+'>'+data.answer[element.id]+'</textarea>'
               }

               if (element.field_type=='date')  {  
                  question_lists+= '<input name="answer['+element.id+']"  value="'+data.answer[element.id]+'" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" placeholder= "mm/dd/yyyy" data-placement="top" title="" '+required[element.required]+'>'
               }
               if (element.field_type=='dropdown') {   
                  
                  var drop_list=JSON.parse(element.dropdown_list);
                    
                    question_lists+='<select name="answer['+element.id+']" class="form-control" '+required[element.required]+'><option value=""> </option>';
                    $.each(drop_list,function(key,value){
                       if (data.answer[element.id]==key ){
                          question_lists+='<option value="'+key+'" selected="selected">'+value+'</option>';  
                        }else{
                          question_lists+='<option value="'+key+'">'+value+'</option>';  
                        }
                    });

                  question_lists+='</select>';

               }

              question_lists+='</div>              </td></tr>';

              

           });
           $('table.lines').append(question_lists);
           $('.date-picker').datepicker();
      });
    }
    
});
</script>
    
@endsection