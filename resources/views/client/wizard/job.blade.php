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
    
      
     
    {!! Form::open(['route' => 'wizard.setjob','autocomplete' => 'off']) !!}
        
         {{ Form::hidden('client_id', $client_id,['id' => 'client_id']) }}
        <?php
        $client = \App\Client::findOrFail($client_id);
        ?>
        <div id="top-wrapper" >
          <br>
          <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step col-xs-2">
                    <a type="button" class="btn btn-success btn-circle">1</a>
                    <p style="color: black"><small><strong>Job/Contract Information</strong></small></p>
                </div>
                <div class="stepwizard-step col-xs-2"> 
                    <a type="button" class="btn btn-default btn-circle">2</a>
                    <p><small>Job/Contract Parties</small></p>
                </div>
                <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle">3</a>
                    <p><small>Attachments</small></p>
                </div>
                <div class="stepwizard-step col-xs-2">    <a type="button" class="btn btn-default btn-circle" >4</a>
                    <p><small>Document to Order</small></p>
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
          <div class="container-fluid requiredfiled">
            <div class="col-xs-12">
              <h4>Only fields in red are required.</h4>
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
                    @if(count($jobs)>1)
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Select "New Job"  and Fill the Information form  or select a Job  from the list below.
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!!  Form::select('job_id',$jobs,old("job_id",$job_id), ['class' => 'form-control','id'=>'job_id']) !!}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @component('client.wizard.dynamicforms.pulljobfromnotice')
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
                    <div class="col-xs-12">
                      <p class="pull_job_error text-danger hidden"> * Job does not exist.</p>
                      <div class="new_job_fields row job_info_group">
                        @if ($job_id == "" )
                            @include('client.wizard.dynamicforms.jobformempty')
                        @else
                            @include('client.wizard.dynamicforms.jobform',['job' => App\Job::FindOrFail($job_id)])
                        @endif    
                      </div>
                    </div>
                    <div class="col-xs-12 text-right job_info_group">
                        @if(session()->has('wizard.newjob'))
                            @if(session('wizard.newjob'))
                            <button class="btn btn-success next-button" type="submit">  <span>  Next </span> <i class="fa fa-chevron-right"></i></button>
                            @else
                            <button class="btn btn-success next-button" type="submit">  <span>  Next </span> <i class="fa fa-chevron-right"></i></button>
                            @endif
                        @else
                        <button class="btn btn-success next-button" type="submit">  <span>  Next </span> <i class="fa fa-chevron-right"></i></button>
                        @endif
                        
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
  var jobNumber = "{{$job_number}}";
  var secretKey = "{{$secret_key}}";
</script>
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
  $('input').keydown(function(){
      $('.btn-success').removeClass("disabled");
      $('.btn-success').css('pointer-events','auto');
  });

  
var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
 
$(function () {
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
      $('.job_info_group').removeClass('hidden');
      $('.pull_job_error').addClass('hidden');
      var xid = $(this).val();
      if (xid==='000') {
        reset2NumbersField();
        $('#modal-pulljobfromnotice').modal('show');
        return;
      }
      $('.new_job_fields').load('{{ url ("/client/wizard/job")}}' + '/' + xid + '/form', function() {
          $('#job_type').trigger('change');
          $('.date-picker').datepicker();
          if (xid == 0) {
              getInterestRate($('#client_id').val());
              getDefaultMaterials($('#client_id').val());
          }
      });
      
  });
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
          $('#modal-pulljobfromnotice').modal('hide');
          $('.new_job_fields').load('{{ url ("/client/wizard/job")}}' + '/' + data + '/form', function() {
            $('#job_type').trigger('change');
            $('.date-picker').datepicker();
          });
        }
    });
  });
  if (jobNumber && secretKey) {
    $('#job_id').val('000');
    $('#job_id').select2();
    //$('#modal-pulljobfromnotice').modal('show');
    $('.btn-pull-job-from-notice').click();
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
$('.address_1').keydown(function(e){
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

</script>
    
@endsection