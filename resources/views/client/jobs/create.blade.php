@extends('client.layouts.app')

@section('css')
<link href="{{ asset('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css">
<style>
  
</style>

@endsection

@section('navigation')
    @include('client.navigation')
@endsection

@section('content')
    {!! Form::open(['route' => 'client.jobs.store','autocomplete' => 'off']) !!}
        
         {{ Form::hidden('redirects_to', Session::get('backUrl')) }}
          {{ Form::hidden('client_id', Auth::user()->client_id) }}
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">New Job/Contract
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit"> <i class="fa fa-floppy-o"></i> Save</button>
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
                 <div class="col-xs-12 message-box">
                     <div class="alert alert-success">
                     By adding this job you are not creating a Work Order. Please click 
                     Create Work Order to create a Work Order. 
                     This is only for tracking your jobs/contracts.    
                     </div>
                     </div>
                <div>&nbsp;</div>
                <div class="row">
                    
                    
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Job Site Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                   
                                        <div class="col-xs-12 col-md-12 form-group">
                                            <label>Job Type:</label>
                                            {!!  Form::select('type',$job_types,old("type"), ['class' => 'form-control','id' => 'job_type']) !!}
                                        </div>
                                </div>
                               <div class="row job-public">
                                    <div class="col-xs-12">
                                    <div class="alert alert-warning" role="alert">
                                        You can not lien a public project, with the exception of a 
                                        leasehold interest, an example would be, 
                                        Starbucks leasing from the county airport.
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
                                                {!!  Form::select('is_tenant',['0' =>'No', '1' => 'Yes','2'=>'I Don\'t Know'],old("is_tenant"), ['class' => 'form-control','id'=>'is_tenant']) !!}
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
                                        <input name="name"  value="{{ old("name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="Descriptive name for the Job">
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Street Address:</label>
                                    <input name="address_1" value="{{ old("address_1")}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="First Line for the address">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2")}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="Second Line for the address">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Address Corner:</label>
                                    <input name="address_corner" value="{{ old("address_corner")}}" placeholder="" class="form-control" data-toggle="tooltip" data-placement="top" title="If you do not know the exact address you can write the closest corner address to the job location">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group hidden">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country", "USA")}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                             <div class="col-md-6 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-6 col-lg-6 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state","FL")}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                </div>
                                
                                <div class="row">
                                     <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>County:</label>
                                    <input id="counties" name="county"  value="{{ old("county")}}" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                
                             
                               
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
        
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Additional Information
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                     <div class="col-md-3 form-group">
                                        <label>Date Started:</label>
                                        <input name="started_at"  value="{{ old("started_at")}}"  data-date-autoclose="true" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="bottom" title="This is the date you started the job.  If this is prefabricated work then the job date starts at the time of prefabrication.">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Last Day on Job:</label>
                                        <input name="last_day"  value="{{ old("last_day")}}" data-date-autoclose="true"  class="form-control date-picker" data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="bottom" title="Punch Work Will Not Extend Your Last Day">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Contract Amount:</label>
                                        {!!  Form::number('contract_amount',old("contract_amount"), ['class' => 'form-control','step'=>'0.01', 'min'=>'0','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'Written or Oral Dollar Amount, This is for your tracking only and will not be included on your Notice to Owner']) !!}
                                    </div> 
                                    <div class="col-md-3 form-group">
                                        <label>Interest rate:</label>
                                        {!!  Form::number('interest_rate',old("interest_rate"), ['class' => 'form-control', 'min'=>'0','step'=>'0.01', 'id'=>"interest_rate"]) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Folio Number:</label>
                                        {!!  Form::text('folio_number',old("folio_number"), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'top', 'title'=>'This is also known as  the parcel id on the tax rolls']) !!}
                                    </div>
                                        <div class="col-md-6 form-group">
                                        <label>Job Number:</label>
                                        {!!  Form::text('number',old("number"), ['class' => 'form-control','data-toggle'=>'tooltip','data-placement'=>'top', 'title'=>'Your job number (if you have one) representing this job/contract']) !!}
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
                                    <label>Legal Descriptions:</label>
                                    {!!  Form::textarea('legal_description',old("legal_description"), ['class' => 'form-control']) !!}
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
$('.btn-success').click(function(){
    $('.btn-success').addClass("disabled");
    $('.btn-success').css('pointer-events','none');
}); 
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
  
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
        
    @if(!old("interest_rate"))
    getInterestRate({{Auth::user()->client_id}});
    @endif
    @if(!old("default_materials"))
    getDefaultMaterials({{Auth::user()->client_id}});
    @endif
    
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
    
    
    
  
});

function getInterestRate(client_id) {
    $.post('{{url("/client")}}/' + client_id + '/interestrate', function( data ) {
         $( "#interest_rate" ).val( data );
    });
}

function getDefaultMaterials(client_id) {
    $.post('{{url("/client")}}/' + client_id + '/defaultmaterials', function( data ) {
         $( "#default_materilas" ).html( data );
    });
}
</script>
    
@endsection