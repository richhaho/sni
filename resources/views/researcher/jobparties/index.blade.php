@extends('researcher.layouts.app')

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/tooltipster/css/tooltipster.bundle.min.css') }}" rel="stylesheet" type="text/css">
<style>
       .tooltip_templates { display: none; } 
       .select2-new-button { padding: 5px; }
</style>
@endsection


@section('content')
            <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="page-header">{{$job->name}}<br><small>{{$job->client->company_name}}</small>
                            <div class="pull-right">
                            @if($work_order == '')
                                <a class="btn btn-success " href="{{ route('jobs.edit',$job->id) }}"><i class="fa fa-chevron-left"></i> Back to Edit Job</a>&nbsp;
                                <a class="btn btn-primary " href="{{ route('jobs.index') }}"> Jobs List</a>&nbsp;
                            @else
                                <a class="btn btn-success " href="{{ route('workorders.edit',$work_order) }}"><i class="fa fa-chevron-left"></i> Back to Work Order</a>&nbsp;
                                <a class="btn btn-primary " href="{{ route('jobs.edit',$job->id) }}"> Edit Job</a>&nbsp;
                            @endif
                             
                            <a class="btn btn-warning " href="{{ route('workorders.setfilter') . '?resetfilter=true&job_filter=' . $job->id }}"> Work Orders List</a>&nbsp;
                            <a class="btn btn-success " href="{{ route('workorders.create') . '?job_id=' . $job->id }}"><i class="fa fa-plus"></i> New Work Order</a>&nbsp;
                            </div>
                        </h1>
                       
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
                        {!! Form::open (['route'=>['parties.store',$job->id],'files' => true]) !!}
                        {!! Form::hidden('job_id',$job->id) !!}
                        {!! Form::hidden('client_id',$job->client->id) !!}
                        {{ Form::hidden('workorder', $work_order) }}
                        <div class="col-md-6 col-lg-5 search-contact">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Add New Job Party</h4>
                                   
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label>Job Party Type:</label>
                                            {!!  Form::select('type',$parties_type,old("type"), ['class' => 'form-control','id' =>'type']) !!}
                                        </div>
                                        <div id="box" class="{{ Session::exists('_old_input') ? array_key_exists("contact_id",Session::get('_old_input')) ? '' : 'hidden' : '' }}">
                                        <div class="col-xs-12 form-group">
                                            <label>Contact:</label>
                                            <select id="contact_id" name="contact_id" class="form-control" style="width: 100%">
                                                 @if(strlen(old("contact_id")) > 0 )
                                                 <option value="{{old("contact_id")}}">{{ Contact::find(old("contact_id"))->name_entity_name}}</option>
                                                 @else
                                                     @if($contact)
                                                    <option value="{{ $contact->id }}">{{ $contact->name_entity_name}}</option>
                                                    @else
                                                    <option value=""></option>
                                                    @endif
                                                 
                                                 @endif
                                            </select>
                                            
                                        </div>
                                        </div>
                                     </div>
                                     <div id="type-fields">
                                        
                                    </div>
                          
                                    <div id="new-contact" class="{{ Session::exists('_old_input') ? array_key_exists("first_name",Session::get('_old_input')) ? '' : 'hidden' : 'hidden' }}">
                                        
                                        @include('researcher.jobparties.dynamicforms.newcontact')
                                    </div>
                                   
                                     
                                    <div class='row'>
                                        
                                        <div class="col-xs-12 form-group ">
                                             <a id="cancel-new-contact-button" class="btn btn-danger hidden">Cancel</a>
                                            {!! Form::submit('Add Job Party',['class'=>'btn btn-success pull-right add_job_party']); !!}
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close(); !!}
                        
                        <div class="col-md-6 col-lg-7">
                                @if (Session::has('message'))
                                    <div class="col-xs-12 message-box">
                                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                                    </div>
                                @endif
                                @foreach ($parties_type as $type_key => $type_name)
                               
                                    @if($loop->first )
                                        <div class="row">
                                    @endif
                                    <div class="col-lg-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">{{ $type_name }}</h4>
                                            </div>
                                            <div class="panel-body">
                                               @foreach($job->parties()->ofType($type_key)->get() as $jobparty) 
                                       
                                                    @include('researcher.jobparties.components.contacticon')

                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @if($loop->iteration % 2 == 0 && $loop->last)
                                       </div>
                                    @else
                                       @if($loop->iteration % 2 == 0)
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

    var job_id = {{ $job->id }};
    var button_added = false;
$('.add_job_party').click(function(){
    $('.add_job_party').addClass("disabled");
}); 
$(function () {
    
    
    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
    
     $('.tooltipster').tooltipster();
     
   $("#contact_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/researcher/jobs")}}/'+ job_id + '/contacts',
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
       $('#type-fields').load('{{ route("additionalform.parties","")}}' +'/'+ xtype,function(){
           $(":file").filestyle();
           $('.date-picker').datepicker();
       });   
   }
   $('#type').on("change",updatetype);
   
   
   
   updatetype();
   
   $('body').on('change','.copy_recipient_type',function () {
       var xval = $(this).val();
       
       if (xval == 'other') {
           $('.other_type').removeClass('hidden');
       } else {
           $('.other_type').addClass('hidden');
           
       }
   });
   
   
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
            url: '{{url("/researcher/jobs")}}/'+ job_id + '/contacts',
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
                            text: item.name_entity_name,
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
</script>
@endsection