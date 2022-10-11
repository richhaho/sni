@extends('client.layouts.app')
@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
<style>
.thumbnail,.message-box{
  word-break: break-word;
}
.tooltip_templates { display: none; } 
.select2-new-button { padding: 5px; }
</style>
@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
  {!! Form::open(['route' => ['client.research.wizard.step4.update',$job->id], 'method'=> 'PUT', 'id'=> 'wizard_form','autocomplete' => 'off']) !!}
  <div id="top-wrapper" >
    <div class="container-fluid">
      <div  class="col-xs-12">
          <h1 class="page-header">Research Wizard Step 4
              <div class="pull-right">
                  <a class="btn btn-danger " href="{{ route('client.research.wizard.step3', $job->id) }}"><i class="fa fa-times-circle"></i> Back</a>
                  @if($job->type=='public')
                  <a class="btn btn-info " href="{{ route('client.research.wizard.step7',$job->id) }}"> Skip to Step 7</a>
                  @endif
                  <button class="btn btn-success " type="submit" form="wizard_form"> <i class="fa fa-floppy-o"></i> Next</button>
              </div>
          </h1>
          <div class="pull-right">
                <label>Research Sites</label>
                {!!  Form::select('match_job',$match_jobs,'', ['class' => 'form-control match_jobs']) !!}
          </div>  
      </div>
    </div>
  </div>
  {!! Form::close() !!}
  <div id="page-wrapper" style="padding-top: 0px">
    <div class="container-fluid row" style="padding-right: 0px">
       <div class="col-xs-12">
            <span style="font-size: 18px"><strong>Attach NOC by clicking into Research Site Clerks and entering into the clerk's website the owner's company name or last name. If we have a GC's name, look up that also. Could the GC be working for a tenant? (If possible add job name as lessee). We are looking for a Notice of Commencement (NOC), Bond Info or any document that appears to play a part in this project. Attach any docs to this page after clicking on Type telling us what type of document it could be.</strong></span>
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
      <div class="col-xs-12 col-md-5 col-lg-5">
        <div class="row">
            {!! Form::open (['route'=>['client.research.wizard.parties.store',$job->id],'files' => true]) !!}
            {!! Form::hidden('job_id',$job->id) !!}
            {!! Form::hidden('client_id',$job->client->id) !!}
            {{ Form::hidden('workorder', $work_order) }}
            {{ Form::hidden('from', 'step4') }}
            <div class="col-md-12 col-lg-12 search-contact">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Add New Job Party</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <label>Job Party Type:</label>
                                {!!  Form::select('type',$parties_type,old("type"), ['class' => 'form-control','id' =>'type','required']) !!}
                            </div>
                            <div class="col-xs-12 form-group">
                                <label>Source:</label>
                                {!! Form::select('source_select',['TR' => 'TR', 'NOC' => 'NOC', 'CL' => 'CL', 'OTHR' => 'OTHR', 'SBZ' => 'SBZ'], [] ,['class' => 'multi-select-source form-control', 'multiple'=>'multiple'])!!}
                                <input type="hidden" name="source" class="source-content" value="">
                            </div>
                            <div id="box" class="{{ Session::exists('_old_input') ? array_key_exists("contact_id",Session::get('_old_input')) ? '' : 'hidden' : '' }}">
                                <div class="col-xs-12 form-group">
                                    <label>Contact: (If entered previously start typing name here. If not, click on contact and New Contact.)</label>
                                    <select id="contact_id" name="contact_id" class="form-control" style="width: 100%">
                                            @if(strlen(old("contact_id")) > 0 )
                                            <option value="{{old("contact_id")}}">{{ Contact::find(old("contact_id"))->full_name}}</option>
                                            @else
                                            <option value=""></option>
                                            @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="type-fields">
                        </div>
                        <div id="new-contact" class="{{ Session::exists('_old_input') ? array_key_exists("first_name",Session::get('_old_input')) ? '' : 'hidden' : 'hidden' }}">
                            @include('client.research.wizard.component.newcontact')
                        </div>
                        <div class='row'>
                            <div class="col-xs-12 form-group ">
                                <a id="cancel-new-contact-button" class="btn btn-danger hidden">Cancel</a>
                                {!! Form::submit('Save',['class'=>'btn btn-success btn-save pull-right']); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close(); !!}
            
            <div class="col-md-12 col-lg-12">
                @foreach ($parties_type1 as $type_key => $type_name)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">{{ $type_name }} @if ($type_name=="Client") (Your Company) @endif</h4>
                                </div>
                                <div class="panel-body">
                                    @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 
                                        @include('client.research.wizard.component.contacticon')
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
      </div>
      <div class="col-xs-12 col-md-7 col-lg-7">
        <div class="row">
            {!! Form::open(['route' => ['client.research.addattachment',$job->id],'files' => true,'class'=>'uploadfile']) !!}
            <div class="col-md-12">
                <div class="col-md-12 form-group filegroup">
                    <label>New File:</label>
                    {!!  Form::file('file','', ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <label>Type:</label>
                    {!!  Form::select('type',$attachment_types,'', ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12 form-group">
                    <label>Description:</label>
                    {!!  Form::textarea('description','', ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12 form-group hidden">

                    <div class="checkbox checkbox-slider--b-flat">
                        <label>
                        <input name="notify" type="checkbox" ><span>Notify Customer</span>
                        </label>
                    </div>
                </div>
                <div class="col-md-12 form-group custom_message_group hidden">
                    <label>Notification Message:</label>
                    {!!  Form::textarea('custom_message','Please find the enclosed document that needs to be signed and notarized - Paying attention to any deadline dates, Thank you.', ['class' => 'form-control custom_message', 'rows'=>'4']) !!}
                </div>
                <div class="col-md-6 form-group hidden">

                    <div class="checkbox checkbox-slider--b-flat">
                        <label>
                        <input name="clientviewable" type="checkbox" ><span>Hide from client</span>
                        </label>
                    </div>
                </div>    
                <div class="col-md-12 form-group ">
                    <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="col-md-12">
                @foreach ($job->attachments as $attach)
                        @if($loop->first)
                        <div class="row">
                    @endif
                    <div class="col-md-6 text-center">
                            <div class="thumbnail">
                                <a href="{{ route('client.research.showattachment',[$job->id,$attach->id])}}">
                            <img class="img-responsive" src="{{ route('client.research.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                            </a>
                            <div class="caption">
                                <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                <p>{{ $attach->description }}</p>
                                @if ($attach->clientviewable != 'yes')
                                <p><strong>Hidden</strong></p>
                                @endif
                                @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                                <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                                @endif
                                <p>
                                @component('client.research.components.deleteattachmentmodal')
                                    @slot('id') 
                                        {{ $attach->id }}
                                    @endslot
                                    @slot('file_name') 
                                        {{ $attach->original_name }}
                                    @endslot
                                @endcomponent
                                @if($attach->file_mime =="application/pdf")
                                <a class="btn btn-xs btn-warning" href="{{route('attachment.print',$attach->id)}}"> <i class="fa fa-print"></i> Print</a>
                                @endif
                                </p>
                            </div>
                            </div>
                    </div>
                        @if($loop->iteration % 4 == 0 && $loop->last)
                        </div>
                        @else
                        @if($loop->iteration % 4 == 0)
                            </div>
                            <div class="row">
                        @else
                            @if($loop->last)
                                </div>
                            @endif
                        @endif
                    @endif
                @endforeach
            </div>
            </div>
       </div>
    </div>
  </div>
@endsection


@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/tooltipster/js/tooltipster.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<script>
$('.btn-next').click(function(){
    $('.btn-next').addClass("disabled");
    $('.btn-next').css('pointer-events','none');
}); 
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
}); 
$('#type').click(function(){
  $('.btn-save').removeClass("disabled");
    $('.btn-save').css('pointer-events','auto');
});
 $('.delete_job_party').click(function(){
    $('.delete_job_party').addClass("disabled");
    $('.delete_job_party').css('pointer-events','none');
}); 

    var job_id = {{ $job->id }};
    var button_added = false;
$(function () {
    
    $('.multi-select-source').multiselect({
        includeSelectAllOption: true,
    });
    $('.multi-select-source').change(function() {
        $('.source-content').val($(this).val().join(','))
    });
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
     $('.tooltipster').tooltipster();
     
   $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/research")}}/'+ job_id + '/wizard/contacts',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove();   
                return {
                    results: $.map(data, function (item) {
                        return {
                            name_entity_name: item.name_entity_name,
                            is_hot: item.is_hot,
                            id: item.id,
                            full_address: item.full_address,
                            text: item.name_entity_name,
                        }
                    })
                };
            }
        },
        templateResult: formatContact
   }); 
   
   
   function formatContact (contact) {
    var hot_str = "";
    if (contact.is_hot ==1) {
        hot_str = '<i class="fa fa-fire"></i>';
    }
    var str = '<span>' + hot_str + ' <b> ' + contact.name_entity_name + '</b><br>' + contact.full_address + '</span>'
    var $state = $(str);
    return $state;
  };
  
   $("#contact_id").on('select2:open',function() {
       if (!button_added ) {
       $(".select2-dropdown").append('<div class="row select2-new-button"><div class="col-xs-4 col-xs-offset-8"><a id="new-contact-button" href="#" class="btn btn-success btn-xs btn-block"><i class="fa fa-plus"></i> New Contact</a></div></div>');
       button_added =true;
       }
   });  
  
  
    function updatetype() {
        var xtype= $('#type').val();
        $('#type-fields').load('{{ route("client.additionalform.parties","")}}' +'/'+ xtype,function(){
           $(":file").filestyle();
           $('.date-picker').datepicker();
           leasetype();
       });   
   }
   $('#type').on("change",function() {
       updatetype();
   });
   
   updatetype();
   
   $('body').on('change','.copy_recipient_type',function () {
       var xval = $(this).val();
       
       if (xval == 'other') {
           $('.other_type').removeClass('hidden');
       } else {
           $('.other_type').addClass('hidden');
           
       }
   });
   
   
   $('body').on('change','.lease-type',function () {
       var xval = $(this).val();
       
       if (xval == 'Lessee') {
           $('.bookpage_number').addClass('hidden');
       } else {
           $('.bookpage_number').removeClass('hidden');
           
       }
   });
   
   function leasetype() {
       var xval = $('.lease-type').val();
       
       if (xval == 'Lessee') {
           $('.bookpage_number').addClass('hidden');
       } else {
           $('.bookpage_number').removeClass('hidden');
           
       }
   }
   leasetype();
   
   $('body').on('click','#new-contact-button', function(){
       $('#contact_id').select2("close");
       $('#new-contact').removeClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',false);
      $('#contact_id').prop('disabled',true)
        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
       $('#cancel-new-contact-button').toggleClass('hidden');
       $('#box').addClass('hidden');
   });
   
    $('body').on('click','#cancel-new-contact-button', function(){
       $('#new-contact').addClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',true);
       $('#box').removeClass('hidden');
       $('#cancel-new-contact-button').toggleClass('hidden')
       $('#contact_id').prop('disabled',false)
       $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/research")}}/'+ job_id + '/wizard/contacts',
            dataType: 'json',
            type: "GET",
            delay: 50,
            data: function (params) {
                $(".select2-dropdown").find('.searching').remove();
                $(".select2-dropdown").prepend('<span class="searching">&nbsp;Searching...</span>');
                return params;
            },
            processResults: function (data) {
                $(".select2-dropdown").find('.searching').remove();   
                return {
                    results: $.map(data, function (item) {
                        return {
                            name_entity_name: item.name_entity_name,
                            id: item.id,
                            full_address: item.full_address,
                            text: item.name_entity_name
                        }
                    })
                };
            }
        },
        templateResult: formatContact
   }); 
        button_added =false;
      
       $('#contact_id').select2("open");
    });
    $('body').on('click','#choose-company-button', function() {
        
        $(".choose-company").removeClass('hidden').find('select').prop('disabled',false);
        $(".new-company").addClass('hidden').find('input').prop('disabled',true);
       
    });
    
    $('body').on('click','#new-company-button', function() {
        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
       
    });
    
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
              
              if($('#new-contact').hasClass('hidden')) {
                  $('#new-contact').find('select,input').prop('disabled',true);
                  $('#box').find('select').prop('disabled',false);
                  $('#cancel-new-contact-button').addClass('hidden')
              } else {
                  $('#new-contact').find('select,input').prop('disabled',false);
                  $('#box').find('select').prop('disabled',true);
                  $('#cancel-new-contact-button').removeClass('hidden')
              }
              
              @if(Session::exists('_old_input'))
                  @if(array_key_exists("firm_name",Session::get('_old_input')))
                        $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
                        $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
                  @else
                        $(".choose-company").removeClass('hidden').find('select').prop('disabled',false);
                        $(".new-company").addClass('hidden').find('input').prop('disabled',true);
                  @endif
                
              @else
                 $(".choose-company").addClass('hidden').find('select').prop('disabled',true);
                 $(".new-company").removeClass('hidden').find('input').prop('disabled',false);
              @endif
              
});
$(".match_jobs").change(function() {
    let url = $(".match_jobs").val();
    if (!url) return;
    url = url.includes('http') ? url : 'http://' + url;
    window.open(url, '_blank');
    $(".match_jobs").val('');
})
</script>
@endsection