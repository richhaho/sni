@extends('admin.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">

@if (!old("county"))
<style type="text/css">
    .address_fields,.wait{
        display: none;
    }
</style>
@endif

@endsection

@section('navigation')
    @include('admin.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => 'jobs.store','autocomplete' => 'off']) !!}
        
         {{ Form::hidden('redirects_to', Session::get('backUrl')) }}
         @if ($contract_tracker)
         {{ Form::hidden('contract_tracker', $contract_tracker->id) }}
         @endif
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Job
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{ Session::get('backUrl')}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
                       
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
                                Client Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    @if ($contract_tracker)
                                    {!!  Form::select('client_id',$clients,$contract_tracker->client_id, ['class' => 'form-control','id'=>'client_id', 'readonly'=>true]) !!}
                                    @else
                                    {!!  Form::select('client_id',$clients,old("client_id"), ['class' => 'form-control','id'=>'client_id']) !!}
                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Job Site Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                   
                                    <div class="col-xs-12 col-md-12 form-group">
                                        <label>Job Type:</label>
                                        {!!  Form::select('type',$job_types,old("type"), ['class' => 'form-control','id'=>'job_type']) !!}
                                    </div>
                                  
                                </div>
                                <div class="row job-public">
                                    <div class="col-xs-12">
                                    <div class="alert alert-warning" role="alert">
                                        You cannot lien a public project unless 
                                        working for a private lessee interest.
                                    </div>
                                    </div>
                                </div>
                                <div class="row job-private">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Private Type:</label>
                                        {!!  Form::select('private_type',['residential' =>'Residential', 'commercial' => 'Commercial'],old("private_type"), ['class' => 'form-control','id'=>'private_type']) !!}
                                    </div>
                                    <div class="ptype-residential">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Is this a Condo?:</label>
                                        {!!  Form::select('is_condo',['0' =>'No', '1' => 'Yes'],old("is_condo"), ['class' => 'form-control','id'=>'is_condo']) !!}
                                    </div>
                                        <div class="is_condo">
                                            <div class="col-xs-12 col-md-6 form-group association_name">
                                                <label>Association Name:</label>
                                                {!!  Form::text('association_name',old("association_name"), ['class' => 'form-control','id'=>'association_name']) !!}
                                            </div>
                                            <div class="col-xs-12 col-md-6 form-group a_unit_number">
                                                <label>Unit #:</label>
                                                {!!  Form::text('a_unit_number',old("a_unit_number"), ['class' => 'form-control','id'=>'a_unit_number']) !!}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="ptype-commercial">
                                    <div class="col-xs-12 col-md-12 form-group">
                                        <label>Is this a Mall Unit?:</label>
                                        {!!  Form::select('is_mall_unit',['0' =>'No', '1' => 'Yes'],old("is_mall_unit"), ['class' => 'form-control','id'=>'is_mall_unit']) !!}
                                    </div>
                                        <div class="is_mall_unit_0">
                                             <div class="col-xs-12 col-md-12 form-group">
                                                <label>Is this a Tenant?:</label>
                                                {!!  Form::select('is_tenant',['0' =>'No', '1' => 'Yes'],old("is_tenant"), ['class' => 'form-control','id'=>'is_tenant']) !!}
                                            </div>
                                        </div>
                                        <div class="is_mall_unit_1">
                                            <div class="col-xs-12 col-md-6 form-group mall_name">
                                                <label>Mall Name:</label>
                                                {!!  Form::text('mall_name',old("mall_name"), ['class' => 'form-control','id'=>'mall_name']) !!}
                                            </div>
                                            <div class="col-xs-12 col-md-6 form-group m_unit_number ">
                                                <label>Unit #:</label>
                                                {!!  Form::text('m_unit_number',old("m_unit_number"), ['class' => 'form-control','id'=>'m_unit_number']) !!}
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    
                                    <div class="col-xs-12 form-group">
                                        <label>Job Name:</label>
                                        @if ($contract_tracker)
                                        <input name="name"  value="{{$contract_tracker->name}}" readonly class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                        @else
                                        <input name="name"  value="{{ old("name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Status:</label>
                                        {!!  Form::select('status',$job_statuses,old("status"), ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Address Source:</label>
                                        {!!  Form::select('address_source',$address_sources,old("address_source"), ['class' => 'form-control']) !!}
                                       
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 form-group">
                                        <label>County:</label>
                                        <input id="counties" name="county"  value="{{ old("county")}}" class="form-control county" maxlength="50" list='county_list' oninput="enteringCounty()">
                                        <datalist id='county_list'>
                                            <script>var counties=[];</script>
                                            @foreach($counties as $county)
                                            <option>{{$county}}</option>
                                            <script>counties.push("{{$county}}");</script>
                                            @endforeach
                                        </datalist>

                                    </div>
                                </div>
                                <div class="row address_fields">
                                    <div class="col-md-12 form-group">
                                        <label class="requiredfiled">Street Address*:</label>
                                        <input name="address_1" value="{{ old("address_1")}}" placeholder="Street and number" class="form-control address_1" data-toggle="tooltip" 
                                        data-placement="bottom"  title="First Line for the address" maxlength="200" list="address_1_list">
                                        <datalist id="address_1_list" class="address_1_list">
                                        </datalist>
                                        <p class="wait">Please wait...</p>
                                    </div>
                                     
                                    <div class="col-md-12 form-group">
                                        <input name="address_2" value="{{ old("address_2")}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control address_2" data-toggle="tooltip" data-placement="bottom"  title="Second Line for the address" maxlength="200" >
                                    </div>
                                     
                                    <div class="col-md-12 form-group">
                                        <label>Address Corner:</label>
                                        <input name="address_corner" value="{{ old("address_corner")}}" placeholder="" class="form-control address_corner" data-toggle="tooltip" data-placement="bottom"  title="If you do not know the exact address you can write the closest corner address to the job location" maxlength="200" >
                                    </div>
                                     
                                    <div class="col-md-12 col-lg-12 form-group hidden">
                                        <label>Country:</label>
                                        <input id="countries" value="{{ old("country", "USA")}}" name="country" class="form-control typeahead country"  autocomplete="off" maxlength="200" >
                                    </div>
                                    <div class="col-md-6 col-lg-6 form-group">
                                        <label class="requiredfiled">City*:</label>
                                        <input name="city"  value="{{ old("city")}}" class="form-control city"  maxlength="200" >
                                    </div>
                                 
                                    <div class="col-md-6 col-lg-6 form-group">
                                        <label>State / Province / Region:</label>
                                        <input id="states" value="FL" name="state" class="form-control typeahead state"  autocomplete="off" maxlength="50" >
                                    </div>
                                    <div class="col-md-12 col-lg-6 form-group">
                                        <label>Zip code:</label>
                                        <input name="zip"  value="{{ old("zip")}}" class="form-control zip" maxlength="50" >
                                    </div>
                                </div>
                                <input name="owner_name" type="hidden" class="owner_name" value="{{ old("owner_name")}}">
                                <input name="owner_address_1" type="hidden" class="owner_address_1" value="{{ old("owner_address_1")}}">
                                <input name="owner_address_2" type="hidden" class="owner_address_2"value= "{{ old("owner_address_2")}}">
                                <input name="owner_city" type="hidden" class="owner_city" value="{{ old("owner_city")}}">
                                <input name="owner_state" type="hidden" class="owner_state" value="{{ old("owner_state")}}">
                                <input name="owner_zip" type="hidden" class="owner_zip" value="{{ old("owner_zip")}}">
                                
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
        
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Additional Information
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>Date Started:</label>
                                        @if ($contract_tracker)
                                        <input name="started_at"  value="{{ date('m/d/Y', strtotime($contract_tracker->start_date))}}" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-date-autoclose="true"  data-toggle="tooltip" data-placement="top" >
                                        @else
                                        <input name="started_at"  value="{{ old("started_at")}}" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-date-autoclose="true"  data-toggle="tooltip" data-placement="top" >
                                        @endif
                                        <!-- title="This is the date you started the job.  If this is prefabricated work then the job date starts at the time of prefabrication." -->
                                    </div>
                                     <div class="col-md-4 form-group">
                                        <label>Last Day on Job:</label>
                                        <input name="last_day"  value="{{ old("last_day")}}" class="form-control date-picker"  data-date-format = "mm/dd/yyyy" data-date-autoclose="true"  data-toggle="tooltip" data-placement="top">
                                         <!-- title="Punch Work Will Not Extend Your Last Day" -->
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Contract Amount:</label>
                                        {!!  Form::number('contract_amount',old("contract_amount"), ['class' => 'form-control contract_amount','step'=>'0.01', 'min'=>'0','text'=>'0','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'Written or Oral Dollar Amount, This is for your tracking only and will not be included on your Notice to Owner']) !!}
                                    </div> 
                                </div>    
                                  <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Folio Number:</label>
                                        {!!  Form::text('folio_number',old("folio_number"), ['class' => 'form-control folio_number','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'This is also known as  the parcel id on the tax rolls']) !!}
                                    </div>
                                        <div class="col-md-6 form-group">
                                        <label>Job Number:</label>
                                        {!!  Form::text('number',old("number"), ['class' => 'form-control','data-placement'=>'top', 'title'=>'Your job number (if you have one) representing this job/contract']) !!}
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>NOC Number:</label>
                                        {!!  Form::text('noc_number',old("noc_number"), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'If you have a copy of the Notice of Commencement add Official Book and Page here - And attach copy to attachments']) !!}
                                    </div>
                                     <div class="col-md-6 form-group pnumber-group">
                                        <label>Project Number:</label>
                                        {!!  Form::text('project_number',old("project_number"), ['class' => 'form-control','id' => 'project_number']) !!}
                                    </div>
                                </div>
                
                                
                                <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Default Materials:</label>
                                    {!!  Form::textarea('default_materials',old("default_materials"), ['class' => 'form-control','id' => 'default_materilas']) !!}
                                </div>
                                </div>
                                 <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Legal Descriptions:</label> <a class="cleanup" data-id="legal-description" href="#">Clean Up</a>
                                    {!!  Form::textarea('legal_description',old("legal_description"), ['class' => 'form-control legal_description','id'=> 'legal-description']) !!}
                                </div>
                                <div class="col-md-12 form-group">
                                    <label>Legal Description Source:</label>
                                    {!!  Form::select('legal_description_source',[''=>'', 'TR'=>'TR', 'NOC'=>'NOC', 'PA'=>'PA'],old("legal_description_source"), ['class' => 'form-control']) !!}
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <!-- /.container-fluid -->
            
        </div>
    {!! Form::close() !!}
@endsection




@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
});
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
  $('#client_id').select2();
  $('.date-picker').datepicker();
  
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
    
   
    @if(!count($errors)>0)
    getInterestRate($('#client_id').val());
    @endif
    @if(!old("default_materials"))
    getDefaultMaterials($('#client_id').val());
    @endif
        
   
   
    
    $('#client_id').on('change',function() {
         getInterestRate($('#client_id').val());
         getDefaultMaterials($('#client_id').val());
    });
  
    $('body').on('click','.cleanup',function() {
        var xid = $(this).data('id');
        var txtValue = $('#' + xid).val();
        var Stext = txtValue.replace(/\n|\r/g, " ").replace(/\n/g, " ").replace(/\r/g, " ");;;
        //$('#' + xid).html(txtValue.replace(/\n|\r/g, " "));
        $('#' + xid).val(Stext);
    });
    $('.address_1').attr('autocomplete','off');
});

function getInterestRate(client_id) {
    $.post('{{url("/admin/clients")}}/' + client_id + '/interestrate', function( data ) {
         $( "#interest_rate" ).val( data );
    });
}

function getDefaultMaterials(client_id) {
    $.post('{{url("/admin/clients")}}/' + client_id + '/defaultmaterials', function( data ) {
         $( "#default_materilas" ).html( data );
    });
}
$('.contract_amount').val('0');


////////////////////// Address Search Part ////////////////
var property_addresses=[];
var county='';var wait=false;
$('.address_1').keydown(function(e){
  if(wait) {e.preventDefault();return;}
  var address_1=$('.address_1').val();
  if(e.keyCode==32 && address_1 && address_1.indexOf(' ')<0){
    county=$('.county').val().toUpperCase();
    if (counties.indexOf(county)<0) return;
    wait=true;$('.wait').css('display','block');
    setTimeout(function(){wait=false;$('.wait').css('display','none');},5000);
    $.get('{{route("jobs.getaddress")}}?county='+county+'&address_1='+address_1 , function( data ) {
      property_addresses=data;
      
      data.forEach(function(item,index){
          setTimeout(function() {
            $('.address_1_list').append('<option value="'+item.property_address_full.toUpperCase()+'">'+item.property_address_1+'</option>');
          }, 1);  
      });
      $('.address_1_list').attr('id','address_1_list');
      wait=false;$('.wait').css('display','none');$('.address_2').focus();$('.address_1').focus();
    }).fail(function() {
         wait=false;$('.wait').css('display','none');
    });; 
  }
});
$('body').on('input','.address_1',function() {
  if($('.address_1').val().indexOf(' ')<0){
    $('.address_1_list').empty();
    //$('.address_1_list').attr('id','address_1_list_noinput');
    return;
  };
  $('.state').val('');
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
    if (item.property_address_full.trim().toUpperCase()==$('.address_1').val().trim() && item.property_county.toUpperCase()==county){
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
    $('.address_1').blur();
    $('.address_1').focus();    
  }

});
function enteringCounty(){
    $('.folio_number').val('');
    $('.legal_description').val('');
    county=$('.county').val().toUpperCase();
    if(!county) {
      $('.address_fields').css('display','none');

      $('.state').val('');
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
    $('.county').blur();
    $('.county').focus();    
  }
});
</script>
    
@endsection