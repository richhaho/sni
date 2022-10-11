@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/css/sidebar.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.css') }}">
<style>
    .tab-pane {
        margin-top: 20px;
    }
    .address_fields,.wait{
        display: none;
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
          <h1 class="page-header">Edit Job
              <div class="pull-right">
                  
                  @if($work_order == '')
                  <a class="btn btn-danger " href="{{ route('jobs.index') }}"><i class="fa fa-times-circle"></i> Exit</a>
                  @else
                  <a class="btn btn-danger " href="{{ route('workorders.edit',$work_order)}}"><i class="fa fa-times-circle"></i> Cancel</a>
                  @endif
                  <a class="btn btn-primary " href="{{ route('jobs.logs',$job->id)}}"><i class="fa fa-eye"></i> View logs</a> &nbsp;&nbsp;
                  <a class="btn btn-info" href="{{ $job->research_complete ? route('jobs.submit.research',$job->id) : '#'}}" {{$job->research_complete ? '':'disabled'}}><i class="fa fa-check"></i> Resubmit to Research Queue</a> &nbsp;&nbsp;
                  <a href="#" data-toggle="modal" data-target="#modal-job-property-search" class="btn btn-warning btn-property-search"><i class="fa fa-search"></i> Property Search</a>&nbsp;&nbsp;
                  @if (!$job->research_complete)
                  <a href="{{ route('jobs.mark_completed',$job->id)}}" class="btn btn-info btn-property-search"><i class="fa fa-check"></i> Mark Research Completed</a>&nbsp;&nbsp;
                  @endif
                  <a href="{{ route('jobs.summary',$job->id)}}" class="btn btn-primary"><i class="fa fa-book"></i> View Job Summary</a>&nbsp;&nbsp;
                  <a href="#" data-toggle="modal" data-target="#modal-job-share" class="btn btn-warning btn-job-share"><i class="fa fa-share"></i> Share</a>&nbsp;&nbsp;
                  <div class="col-xs-12">
                  </div>
                  &nbsp;
              </div>
          </h1>

          <div class="modal fade" id="modal-job-share" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Share job to mornitoring user</h4>
                </div>
                {!! Form::open(['route' => ['jobs.share',$job->id]]) !!}
                <div class="modal-body">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-12 form-group">
                          <label>Mornitoring User Email:</label>
                          <input name="email" type="email" value="" class="form-control" maxlength="200" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button class="btn btn-primary btn-share-job" type="submit"><i class="fa fa-save"></i> Save</button>
                </div>
                {!! Form::close() !!}
              </div>
            </div>
          </div>

          <div class="modal fade" id="modal-job-property-search" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Property Search</h4>
                </div>
                {!! Form::open(['route' => ['jobs.save_property',$job->id]]) !!}
                <div class="modal-body">

                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-12 form-group">
                          <label style="color: red">County*:</label>
                          <input id="county" name="county"  value="{{$job->county}}" class="form-control county" maxlength="50" required list='county_list' oninput="enteringCounty()">
                          <datalist id='county_list'>
                              <script>var counties=[];</script>
                              @foreach($counties as $county)
                              <option>{{$county}}</option>
                              <script>counties.push("{{$county}}");</script>
                              @endforeach
                          </datalist>
                      </div>
                      <div class="col-md-12 form-group">
                          <label style="color: red">Search by full address*:</label><br>
                          <input name="full_address" value="" class="form-control full_address"  maxlength="200" list="full_address_list">
                          <datalist id="full_address_list" class="full_address_list">
                          </datalist>
                          <p class="wait">Please wait...</p>
                      </div>
                    </div>
                  </div>

                  <div class="panel-body">
                    <div class="row">
                      <div class="col-md-12 form-group">
                          <label>Street Address:</label>
                          <input name="address_1" value="{{$job->address_1}}" placeholder="" class="form-control address_1" maxlength="200" required placeholder="Street and number">
                      </div>
                       
                      <div class="col-md-12 form-group">
                          <input name="address_2" value="{{$job->address_2}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control address_2"  maxlength="200" >
                      </div>
                       
                      <div class="col-md-6 col-lg-6 form-group">
                          <label class="requiredfiled">City:</label>
                          <input name="city"  value="{{$job->city}}" class="form-control city"  maxlength="200" required>
                      </div>
                   
                      <div class="col-md-6 col-lg-6 form-group">
                          <label>State / Province / Region:</label>
                          <input id="states" value="{{$job->state}}" name="state" class="form-control typeahead state"  autocomplete="off" maxlength="50" >
                      </div>
                       
                      <div class="col-md-12 col-lg-6 form-group">
                          <label>Zip code:</label>
                          <input name="zip"  value="{{$job->zip}}" class="form-control zip" maxlength="50" >
                      </div>
                    
                    </div>
                    <input name="owner_name" type="hidden" class="owner_name" value="">
                    <input name="owner_address_1" type="hidden" class="owner_address_1" value="">
                    <input name="owner_address_2" type="hidden" class="owner_address_2"value= "">
                    <input name="owner_city" type="hidden" class="owner_city" value="">
                    <input name="owner_state" type="hidden" class="owner_state" value="">
                    <input name="owner_zip" type="hidden" class="owner_zip" value=""> 
                    <input name="folio_number" type="hidden" class="folio_number" value=""> 
                    <input name="legal_description" type="hidden" class="legal_description" value=""> 
                  </div>


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button class="btn btn-success btn-property-save" type="submit" disabled><i class="fa fa-save"></i> Save</button>
                </div>
                {!! Form::close() !!}
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="btn-group" role="group" aria-label="">
        <a href="{{ route('parties.index',$job->id) }}" class="btn btn-default"><i class="fa fa-users"></i> Parties Assignment</a>
        @if($job->status != 'closed')
        <a href="{{ route('workorders.create',['job_id' =>$job->id])}}" class="btn btn-default"><i class="fa fa-briefcase"></i> Create Work Order</a>
        @endif
        <a href="{{ route('workorders.setfilter') . '?resetfilter=true&job_filter=' . $job->id }}" class="btn btn-default"><i class="fa fa-eye"></i> View Work Orders</a>
        <a class="btn btn-default " onclick="openNav()"><i class="fa fa-clone fa-fw"></i> Copy Data from Existing Job </a>
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
     <!-- /.Attachments -->
      <div class="col-xs-12">
          <!-- Nav tabs -->
          <div class="alert alert-danger">Before Adding a note, attachment, change or payment, please SAVE YOUR CHANGES</div>
          <div>&nbsp;</div>
          <ul class="nav nav-tabs" role="tablist" id="job_tabs" data-tabs="tabs">
            <li role="presentation" class="active"><a href="#jobform" aria-controls="messages" role="tab" data-toggle="tab">Job Info</a></li>
            <li role="presentation"><a href="#parties" aria-controls="messages" role="tab" data-toggle="tab">Job Parties</a></li>
            <li role="presentation"><a href="#changes" aria-controls="profile" role="tab" data-toggle="tab">Job Change Orders</a></li>
            <li role="presentation"><a href="#payments" aria-controls="messages" role="tab" data-toggle="tab">Job Payments</a></li>
            <li role="presentation"><a href="#workorders" aria-controls="messages" role="tab" data-toggle="tab">Work Orders</a></li>
            <li role="presentation"><a href="#questions" aria-controls="messages" role="tab" data-toggle="tab">Additional Questions Summary</a></li>
            <li role="presentation"><a href="#notes" aria-controls="messages" role="tab" data-toggle="tab">Notes</a></li>
            <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments</a></li>
            <li role="presentation"><a href="#reminders" aria-controls="messages" role="tab" data-toggle="tab">Reminders</a></li>
            <li role="presentation"><a href="#nocs" aria-controls="messages" role="tab" data-toggle="tab">Job NOCs</a></li>
            <li role="presentation"><a href="#linked" aria-controls="messages" role="tab" data-toggle="tab">Linked Jobs</a></li>
          </ul>
         
          <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="jobform">
                @include('admin.jobs.jobform')
              </div>
              <div role="tabpanel" class="tab-pane" id="parties">
                  @include('admin.jobs.components.jobparties-tab')
              </div>
              <div role="tabpanel" class="tab-pane" id="workorders">
                @include('admin.jobs.workorders.index',['works' => $job->workorders()->where('status', '!=', 'temporary')->get()])
              </div>
              <div role="tabpanel" class="tab-pane" id="questions">
                @include('admin.jobs.workorders.question',['works' => $job->workorders()->where('status', '!=', 'temporary')->get()])
              </div>
               <div role="tabpanel" class="tab-pane " id="notes">
                 @include('admin.notes.index', ['notes' => $job->notes()->where('deleted_at',null)->orderBy('entered_at','des')->get(),'e_name' => 'jobs','e_id' => $job->id])
              </div>
              <div role="tabpanel" class="tab-pane" id="attachments">
                @include('admin.jobs.components.attachments-tab')
              </div>
              <div role="tabpanel" class="tab-pane" id="changes">
                  @include('admin.jobs.changes.index', ['changes' => $job->changes()->orderBy('added_on','des')->get()])
              </div>
              <div role="tabpanel" class="tab-pane" id="payments">
                 @include('admin.jobs.payments.index', ['payments' => $job->payments()->orderBy('payed_on','des')->get()])
              </div>
              <div role="tabpanel" class="tab-pane" id="reminders">
                  @include('admin.jobs.reminders.index', ['reminders' => $job->reminders()->orderBy('sent_at','asc')->get()])
              </div>
              <div role="tabpanel" class="tab-pane" id="nocs">
                  @include('admin.jobs.nocs.index', ['job'=> $job, 'nocs' => $job->nocs()->orderBy('recorded_at','desc')->get()])
              </div>        
              <div role="tabpanel" class="tab-pane" id="linked">
                  @include('admin.jobs.linked.index', ['job'=> $job, 'linked_jobs' => $job->linked_jobs()->orderBy('created_at','desc')->get()])
              </div>     
          </div>
      </div>
     <!-- /.notes -->
    </div>
        <!-- /.container-fluid -->
  </div>
@endsection

@section('sidebar')
@include('admin.jobs.sidebar')
@endsection



@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/sidebar.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<?php
$max_uploadfileSize= min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_uploadfileSize= substr($max_uploadfileSize, 0, -1)*1024*1024;
?>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
 var job_id = {{ $job->id }};
 $('.btn-success').click(function(){
    $('.btn-success').addClass("disabled");
    $('.btn-success').css('pointer-events','none');
});
$(function () {
    $('.multi-select-email').multiselect({
        includeSelectAllOption: true,
    });
    $('.multi-select-email').change(function() {
        const el = $(this).parent().parent();
        el.find('.email-content').val($(this).val().join(','))
        el.find('.emails-label').text($(this).val().join(',  '))
        if ($(this).val().length>0) {
          el.find('.reminder-email-required').addClass('hidden');
        } else {
          el.find('.reminder-email-required').removeClass('hidden');
        }
    });
    $("input[type='file']").attr('accept', '.pdf,.jpg,.jpeg,.tiff,.tif,.doc,.xls,.docx,.xlsx');
    $('input[type="number"]').keydown( function(e){
      if(e.keyCode==188){
        e.preventDefault();
      };
    });
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

    $('.custom_message_group').css('display', 'none');
    $("input[name='notify']").change(function(){
        if ($("input[name='notify']").prop('checked')) {
          $('.custom_message_group').css('display', 'block');
        } else {
          $('.custom_message_group').css('display', 'none');
        }
    });

    $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
    });
  $('[data-toggle="tooltip"]').tooltip()
  $('#client_id').select2();
  $('.date-picker').datepicker();
   $(":file").filestyle();
   var hash = window.location.hash;
   if (hash.length > 0 ) {
        $('#page-wrapper').scrollTop($(hash).offset().bottom);
        
   }
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
    
    $('#job_type').on('change',function() {
        console.log('cambio');
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
    
     $('#private_type').on('change',function() {
           var xval = $(this).val();
        $('div[class*="ptype-"]').hide();
        $('div[class*="ptype-' + xval + '"]').show();
        
        if (xval == "residential") {
             $('#is_condo').trigger('change');
        } else {
             $('#is_mall_unit').trigger('change');
        }
     });
     
     
      $('#is_condo').on('change',function() {
           var xval = $(this).val();
           if (xval == "1") {
             $('.is_condo').show();
             
           } else {
             $('.is_condo').hide();
           }
     });
    
    
      $('#is_mall_unit').on('change',function() {
           var xval = $(this).val();
            $('div[class*="is_mall_unit_"]').hide();
            $('div[class*="is_mall_unit_' + xval + '"]').show();
           
     });
    
    
    $('#job_type').trigger('change');
    
    
    
    
     $('#states').typeahead(null, {
      name: 'states',
      source: states
    });
    
    $('#states').focus(function () {
        states.initialize(true);
    }); 
    
    var counties = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // url points to a json file that contains an array of country names, see
        // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
        //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
        prefetch:  { url: '{{ route('list.counties') }}' , cache: false }
      });
      
    $('#counties').typeahead(null, {
      name: 'counties',
      source: counties
    });
    
     var address_sources = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // url points to a json file that contains an array of country names, see
        // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
        //local: ['Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica'],
        prefetch:  { url: '{{ route('list.addresssources') }}' , cache: false }
      });
      
    $('#address_source').typeahead(null, {
      name: 'address_source',
      source: address_sources
    });
        
        $('#job_tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      })
      
    var hash = window.location.hash;
    //console.log(hash);
    if (hash.length > 0 ) {
         $('#job_tabs a[href="' + hash + '"]').tab('show')
         $('#page-wrapper').scrollTop($(hash).offset().top);
    }
    
    // getInterestRate($('#client_id').val());
    // getDefaultMaterials($('#client_id').val());
    
    $('#client_id').on('change',function() {
         getInterestRate($('#client_id').val());
         getDefaultMaterials($('#client_id').val());
    });
  
    // here we start search autocomplete of JOBS like contacts
  
    $("#copy_job_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        //maximumInputLength: 20,

        ajax: {
            url: '{{url("/admin/jobs")}}/'+ job_id + '/copy',
            dataType: 'json',
            type: "GET",
            delay: 50,
            
            processResults: function (data) {
                 
                return {
                    results: $.map(data, function (item) {
                        return {
                            name: item.name,
                            id: item.id,
                            full_address: item.full_address,
                            text: item.name
                        }
                    })
                };
            }
        },
        templateResult: formatJob
   });
   
   function formatJob (job) {
       
        var str = '<span><b> ' + job.name + '</b><br>' + job.full_address + '</span>'
        var $state = $(str);
        return $state;
    };
    
    
    
    $("#copy_job_id").on('change',function() {
        var job_id = $(this).val();
        $('.copy_data').load('{{url("/admin/jobs")}}/' + job_id +'/copyform');
    });
    
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

    $('.full_address').attr('autocomplete','off');
  
});

function getInterestRate(client_id) {
    if ($( "#interest_rate" ).val()) {
      return;
    }
    $.post('{{url("/admin/clients")}}/' + client_id + '/interestrate', function( data ) {
         $( "#interest_rate" ).val( data );
    });
}

function getDefaultMaterials(client_id) {
    $.post('{{url("/admin/clients")}}/' + client_id + '/defaultmaterials', function( data ) {
         $( "#default_materilas" ).html( data );
    });
}

 $('body').on('click','.cleanup',function() {
        var xid = $(this).data('id');
        var txtValue = $('#' + xid).val();
        var Stext = txtValue.replace(/\n|\r/g, " ").replace(/\n/g, " ").replace(/\r/g, " ");;;
        //$('#' + xid).html(txtValue.replace(/\n|\r/g, " "));
        $('#' + xid).val(Stext);
    });
</script>
<script type="text/javascript">
    $('.btn-close').click(function(){
        $("#modal_multiple_response").css('display','none');
    });
</script>   

<script type="text/javascript">
////////////////////// Address Search Part ////////////////
var property_addresses=[];
var county='';var wait=false;
$('.full_address').keydown(function(e){
  if(wait) {e.preventDefault();return;}
  var full_address=$('.full_address').val();
  if(e.keyCode==32 && full_address && full_address.indexOf(' ')<0){
    county=$('.county').val().toUpperCase();
    if (counties.indexOf(county)<0) return;
    wait=true;$('.wait').css('display','block');
    setTimeout(function(){wait=false;$('.wait').css('display','none');},5000);
    $.get('{{route("jobs.getaddress")}}?county='+county+'&address_1='+full_address , function( data ) {
      property_addresses=data;
      
      data.forEach(function(item,index){
          setTimeout(function() {
            $('.full_address_list').append('<option value="'+item.property_address_full.toUpperCase()+'">'+item.property_address_1+'</option>');
          }, 1);
      });
      $('.full_address_list').attr('id','full_address_list');
      wait=false;$('.wait').css('display','none');$('.address_1').focus();$('.full_address').focus();
    }).fail(function() {
         wait=false;$('.wait').css('display','none');
    });; 
  }
});
$('body').on('input','.full_address',function() {
  if($('.full_address').val().indexOf(' ')<0){
    $('.full_address_list').empty();
    //$('.full_address_list').attr('id','full_address_list_noinput');
    return;
  };
  // $('.state').val('');
  // $('.zip').val('');
  // $('.city').val('');
  // $('.address_2').val('');
  // $('.address_corner').val('');
  // $('.folio_number').val('');
  // $('.legal_description').val('');
 
  $('.owner_name').val('');
  $('.owner_address_1').val('');
  $('.owner_address_2').val('');
  $('.owner_city').val('');
  $('.owner_state').val('');
  $('.owner_zip').val('');
  $('.btn-property-save').attr('disabled',true);
  property_addresses.forEach(function(item,index){
    if (item.property_address_full.trim().toUpperCase()==$('.full_address').val().trim().toUpperCase() && item.property_county.toUpperCase()==county){
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
      $('.btn-property-save').attr('disabled',false);
    }
  });
  if (navigator.appVersion.indexOf('Edge') > -1){
    $('.full_address').blur();
    $('.full_address').focus();    
  }
});
function enteringCounty(){
    $('.folio_number').val('');
    $('.legal_description').val('');
    county=$('.county').val().toUpperCase();
    // if(!county) {
    //   $('.address_fields').css('display','none');

    //   $('.state').val('');
    //   $('.zip').val('');
    //   $('.city').val('');
    //   $('.address_1').val('');
    //   $('.address_2').val('');
    //   $('.address_corner').val('');
    //   return; 
    // }
    // $('.address_fields').css('display','block');

    $('.full_address_list').empty();
    //$('.full_address_list').attr('id','full_address_list_noinput');
    $('.btn-property-save').attr('disabled',true);
}


$('body').on('input','.county',function() {
  if (navigator.appVersion.indexOf('Edge') > -1){
    $('.county').blur();
    $('.county').focus();    
  }
});
</script>   
@endsection