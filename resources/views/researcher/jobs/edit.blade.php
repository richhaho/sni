@extends('researcher.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/css/sidebar.css') }}" rel="stylesheet" type="text/css">
<style>
      .tab-pane {
        margin-top: 20px;
    }
</style>

@endsection

@section('navigation')
    @include('researcher.navigation')
@endsection

@section('content')
  
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit Job
                    <div class="pull-right">
                        
                        @if($work_order == '')
                        <a class="btn btn-danger " href="{{ route('jobs.index') }}"><i class="fa fa-times-circle"></i> Exit</a> &nbsp;&nbsp;
                        @else
                        <a class="btn btn-danger " href="{{ route('workorders.edit',$work_order)}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
                        @endif
                          <div class="col-xs-12">
                    
                </div>
                   &nbsp;
                    </div>
                </h1>       
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
                    
                    <a class="btn btn-success " onclick="openNav()"><i class="fa fa-clone fa-fw"></i> Copy Data from Existing Job </a>
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
                          <li role="presentation"><a href="#notes" aria-controls="messages" role="tab" data-toggle="tab">Notes</a></li>
                          <li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments</a></li>
                          
                          
                        </ul>
                       
                        <div class="tab-content">
                             <div role="tabpanel" class="tab-pane active" id="jobform">
                              @include('researcher.jobs.jobform')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="parties">
                                @include('researcher.jobs.components.jobparties-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="workorders">
                              @include('researcher.jobs.workorders.index',['works' => $job->workorders()->where('status', '!=', 'temporary')->get()])
                            </div>
                             <div role="tabpanel" class="tab-pane " id="notes">
                               @include('researcher.notes.index', ['notes' => $job->notes()->orderBy('entered_at','des')->get(),'e_name' => 'jobs','e_id' => $job->id])
                            </div>
                            <div role="tabpanel" class="tab-pane" id="attachments">
                              @include('researcher.jobs.components.attachments-tab')
                            </div>
                            <div role="tabpanel" class="tab-pane" id="changes">
                                @include('researcher.jobs.changes.index', ['changes' => $job->changes()->orderBy('added_on','des')->get()])
                            </div>
                            <div role="tabpanel" class="tab-pane" id="payments">
                               @include('researcher.jobs.payments.index', ['payments' => $job->payments()->orderBy('payed_on','des')->get()])
                            </div>
                           
                        </div>
                    </div>
    
               
               <!-- /.notes -->
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    


@endsection

@section('sidebar')
@include('researcher.jobs.sidebar')
@endsection



@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/sidebar.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
 var job_id = {{ $job->id }};
$(function () {
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
    
     getInterestRate($('#client_id').val());
    //getDefaultMaterials($('#client_id').val());
    
    $('#client_id').on('change',function() {
         getInterestRate($('#client_id').val());
         getDefaultMaterials($('#client_id').val());
    });
  
    // here we start search autocomplete of JOBS like contacts
  
    $("#copy_job_id").select2({
        theme:'bootstrap',
        minimumInputLength: 2,
        ajax: {
            url: '{{url("/researcher/jobs")}}/'+ job_id + '/copy',
            dataType: 'json',
            type: "GET",
            delay: 50,
            
            processResults: function (data) {
                console.log(data);
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
        $('.copy_data').load('{{url("/researcher/jobs")}}/' + job_id +'/copyform');
    });
  
});

function getInterestRate(client_id) {
    $.post('{{url("/researcher/clients")}}/' + client_id + '/interestrate', function( data ) {
         $( "#interest_rate" ).val( data );
    });
}

function getDefaultMaterials(client_id) {
    $.post('{{url("/researcher/clients")}}/' + client_id + '/defaultmaterials', function( data ) {
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
    
@endsection