@extends('client.layouts.app')

@section('navigation')
    @include('client.navigation')
@endsection

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
<style>
       .tooltip_templates { display: none; } 
       .select2-new-button { padding: 5px; }
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


@section('content')
            <div id="page-wrapper">
                <br>
                <div class="stepwizard">
                  <div class="stepwizard-row setup-panel">
                      <div class="stepwizard-step col-xs-4">
                          <a type="button" class="btn   btn-default btn-circle">1</a>
                          <p><small>Job/Contract Information</small></p>
                      </div>
                      <div class="stepwizard-step col-xs-4"> 
                          <a type="button" class="btn btn-success btn-circle">2</a>
                          <p style="color: black"><small><strong>Job/Contract Parties</strong></small></p>
                      </div>
                      <div class="stepwizard-step col-xs-4">    <a type="button" class="btn btn-default btn-circle">3</a>
                          <p><small>Attachments</small></p>
                      </div>
                  </div>
                </div>  
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h3 class="page-header">
                            New Work Order - Who is your contract with?<br>
                            <span class="h5">Job: {{$job->name}}</span>
                            <div class="pull-right">
                          
                            <a class="btn btn-danger " href="{{ route('wizard.createjob') . '?job_id=' . $job->id }}"><i class="fa fa-chevron-left"></i> Back</a> 
                            <button type="submit" class="btn btn-success btn-next" form="employer_form" > Next <i class="fa fa-chevron-right"></i></button>&nbsp;
                            
                            </div>
                        </h3>
                       
                    </div>
                    <div>&nbsp;</div>
                    @if (count($errors) > 0)
                    <div class="col-xs-12 message-box">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    <div class="row">
                        <!-- <div class="col-xs-12 text-left"><a href="{{route('wizard.getparties',$job->id)}}" class="btn btn-success btn-skip">Skip Wizard<br></a></div> -->

                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">&nbsp;</h4>
                                </div>
                                <div class="panel-body">

                                    {!! Form::open (['route'=>['wizard.employer.store',$job->id],'files' => true,'id'=> 'employer_form','autocomplete'=>'off']) !!}
                                    {!! Form::hidden('job_id',$job->id) !!}
                                    {!! Form::hidden('client_id',$job->client->id) !!}
                                    {{ Form::hidden('workorder', $work_order) }}
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="h3">Who is your contract with?</label>
                                            @if ($job->client->default_customer_type)
                                            {!!  Form::select('type',$parties_type,old("type",$job->client->default_customer_type), ['class' => 'form-control','id' =>'type']) !!}
                                            @else
                                            {!!  Form::select('type',$parties_type,old("type"), ['class' => 'form-control','id' =>'type']) !!}
                                            @endif
                                        </div>
                                      
                                        <div class="col-xs-12" id="type-fields">
                                        
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
                                     
                          
                                    <div id="new-contact" class="{{ Session::exists('_old_input') ? array_key_exists("first_name",Session::get('_old_input')) ? '' : 'hidden' : 'hidden' }}">
                                        
                                        @include('client.wizard.dynamicforms.newcontact')
                                    </div>
                                   
                                     {!! Form::close() !!}
                                    <div class='row hidden' id="cancelsubmit-new-contact">
                                        <div class="col-xs-6 form-group text-left">
                                            <a  class="btn btn-danger " id="cancel-new-contact-button">Cancel</a>&nbsp;
                                        </div>
                                        <div class="col-xs-6 form-group text-right">
                                            <button type="submit" class="btn btn-success btn-next" form="employer_form"> Next </i></button>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    
                    </div>
                    
         
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/tooltipster/js/tooltipster.bundle.min.js') }}" type="text/javascript"></script>
<script>
$('.btn-skip').click(function(){
    $('.btn-skip').addClass("disabled");
    $('.btn-skip').css('pointer-events','none');
}); 
$('.btn-contact').click(function(){
    $('.btn-contact').addClass("disabled");
    $('.btn-contact').css('pointer-events','none');
}); 
$('.btn-next').click(function(){
  $('#employer_form').submit();
    $('.btn-next').addClass("disabled");
    $('.btn-next').css('pointer-events','none');
});
    var job_id = {{ $job->id }};
    var button_added = false;
$(function () {
    
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
     $('.tooltipster').tooltipster();
     
   $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/wizard/job")}}/'+ job_id + '/contacts',
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
                            text: item.name_entity_name
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
       $(".select2-dropdown").append('<div class="row select2-new-button"><div class="col-xs-4 col-xs-offset-8"><a id="new-contact-button" href="#" class="btn btn-success btn-contact btn-xs btn-block"><i class="fa fa-plus"></i> New Contact</a></div></div>');
       button_added =true;
       }
   });  
  
  
  function updatetype() {
        var xtype= $('#type').val();
       $('#type-fields').load('{{ route("wizard.additionalform","")}}' +'/'+ xtype,function(){
           $(":file").filestyle();
           $('.date-picker').datepicker();
           leasetype();
       });   
       if(xtype == 'general_contractor') {
           $('.gc_group').removeClass('hidden');
           $('.gc_group select').prop('disabled',false)
       } else {
           $('.gc_group').addClass('hidden');
           $('.gc_group select').prop('disabled',true)
       }
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
       $('#cancelsubmit-new-contact').toggleClass('hidden');
       $('#box').addClass('hidden');
   });
   
    $('body').on('click','#cancel-new-contact-button', function(){
       $('#new-contact').addClass('hidden');
       $('#new-contact').find('select,input').prop('disabled',true);
       $('#box').removeClass('hidden');
       $('#cancelsubmit-new-contact').toggleClass('hidden')
       $('#contact_id').prop('disabled',false)
       $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/client/wizard/job")}}/'+ job_id + '/contacts',
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
                  $('#cancelsubmit-new-contact').addClass('hidden')
              } else {
                  $('#new-contact').find('select,input').prop('disabled',false);
                  $('#box').find('select').prop('disabled',true);
                  $('#cancelsubmit-new-contact').removeClass('hidden')
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
</script>
@endsection