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
    
         
        <div id="top-wrapper" >
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h1 class="page-header">Edit {{ $parties_type[$job_party->type] }}  <small> from {{ $job_party->job->name }}</small>
                    <div class="pull-right">
                        <button class="btn btn-success btn-save" type="submit" form="edit_form"> <i class="fa fa-floppy-o"></i> Save</button>
                        <a class="btn btn-danger " href="{{route('client.parties.index',$job_party->job->id)}}"><i class="fa fa-times-circle"></i> Cancel</a> &nbsp;&nbsp;
                    </div>
                </h1>       
            </div>
            </div>
            <div class="container-fluid">
            <div  class="col-xs-12">
                <h5 style="color: #46a1d8">
                    WARNING: You are modifying a contact record.  Any changes you make here will be reflected in both your contact list and ALL jobs this contact is associated with.
                </h5>       
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
                {!! Form::open(['route' => ['client.parties.update',$job_party->job_id,$job_party->id], 'method'=> 'PUT', 'id'=> 'edit_form','autocomplete' => 'off','files' => true]) !!}
                {{ Form::hidden('workorder', $work_order) }}
                <div class="row">
                    <div class="col-xs-12">
                        @if ($job_party->contact->primary==2)
                        <div class="panel panel-default" style="pointer-events:none;opacity: 0.7;">
                        @else 
                        <div class="panel panel-default">
                        @endif
                            <div class="panel-heading">
                                Contact Info
                            </div>
                            <div class="panel-body">
                                @if($job_party->contact->hot_id == 0 )
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12 form-group">
                                            <label> Firm Name(or Individual Name):</label>
                                            @if (count($job_party->firm->contacts->where('primary',2))>0 || $job_party->contact->entity->hot_id>0)
                                                {!!  Form::text('firm_name',$job_party->firm->firm_name, ['class' => 'form-control','maxlength'=>'200','style'=>'pointer-events:none;opacity: 0.7;']) !!}
                                            @else
                                                {!!  Form::text('firm_name',$job_party->firm->firm_name, ['class' => 'form-control','maxlength'=>'200']) !!}
                                            @endif
                                        </div>
                                     </div>
                                    <div class="row">
                                        <div class="col-xs-6 col-md-6 form-group">
                                            <label> First Name:</label>
                                            {!!  Form::text('first_name',$job_party->contact->first_name, ['class' => 'form-control','maxlength'=>'80']) !!}
                                        </div>
                                         <div class="col-xs-6 col-md-6 form-group">
                                            <label> Last Name:</label>
                                            {!!  Form::text('last_name',$job_party->contact->last_name, ['class' => 'form-control','maxlength'=>'80']) !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="row" style="pointer-events:none;opacity: 0.7;">
                                        <div class="col-xs-12 col-md-12 form-group">
                                            <label> Firm Name(or Individual Name):</label>
                                            {!!  Form::text('firm_name',$job_party->firm->firm_name, ['class' => 'form-control','maxlength'=>'  100']) !!}
                                        </div>
                                    </div>
                                    <div class="row" style="pointer-events:none;opacity: 0.7;">
        
                                        <div class="col-xs-6 col-md-6 form-group" >
                                            <label> First Name:</label>
                                            {!!  Form::text('first_name',$job_party->contact->first_name, ['class' => 'form-control','maxlength'=>'80']) !!}
                                        </div>
                                        <div class="col-xs-6 col-md-6 form-group">
                                            <label> Last Name:</label>
                                            {!!  Form::text('last_name',$job_party->contact->last_name, ['class' => 'form-control','maxlength'=>'80']) !!}
                                        </div>
                                    </div>
                                @endif
                                
                                 <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Email:</label>
                                    <input name="email" value="{{ old("email",$job_party->contact->email)}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="100">
                                </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Phone:</label>
                                    <input name="phone" value="{{ old("phone", $job_party->contact->phone)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="30">
                                </div>
                              
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Mobile:</label>
                                    <input name="mobile" value="{{ old("mobile", $job_party->contact->mobile)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="30">
                                </div>
                                <div class="col-md-12 col-lg-4 form-group">
                                    <label>Fax:</label>
                                    <input name="fax" value="{{ old("fax", $job_party->contact->fax)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="30">
                                </div>
                                </div>
                                
                                <div class="row">
                                <!--<div class="col-md-6 form-group">
                                    <label>Gender:</label>
                                    {!!  Form::select('gender',['female' => 'Female', 'male'=> 'Male'],old("gender", $job_party->contact->gender), ['class' => 'form-control']) !!}
                                </div>-->
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>Street Address:</label>
                                        <input name="address_1" value="{{ old("address_1",$job_party->contact->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="200">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <input name="address_2" value="{{ old("address_2",$job_party->contact->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="200">
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-md-12 col-lg-6 form-group">
                                        <label>Country:</label>
                                        <input id="countries" value="{{ old("country",$job_party->contact->country)}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off" maxlength="50">
                                    </div>

                                    <div class="col-md-12 col-lg-6 form-group">
                                        <label>State / Province / Region:</label>
                                        <input id="states" value="{{ old("state",$job_party->contact->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off" maxlength="50">
                                    </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city",$job_party->contact->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="200">
                                </div>
                             
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip",$job_party->contact->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    @if ($job_party->type =="bond" ||$job_party->type =="landowner" || $job_party->type =="leaseholder" || $job_party->type =="copy_recipient")
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {{ $parties_type[$job_party->type] }} Additional Information
                            </div>
                            <div class="panel-body">
                                @if ($job_party->type =="copy_recipient" )
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <label>Copy Recipient Type:</label>
                                            {!! Form::select('copy_recipient_type',['architect' => 'Architect', 'condo assoc' => 'Condo Assoc', 'Developer' => 'Developer', 'engineer' => 'Engineer', 'government agency' => 'Government Agency', 'homeowners assoc' => 'Homeowners Assoc', 'management co' => 'Management Co', 'surveying co' => 'Surveying Co', 'owner' => 'Owner', 'owner designated' => 'Owner Designated','other' => 'Other'], ((array_key_exists($job_party->copy_type,['architect' => 'Architect', 'condo assoc' => 'Condo Assoc', 'Developer' => 'Developer', 'engineer' => 'Engineer', 'government agency' => 'Government Agency', 'homeowners assoc' => 'Homeowners Assoc', 'management co' => 'Management Co', 'surveying co' => 'Surveying Co', 'owner' => 'Owner', 'owner designated' => 'Owner Designated','other' => 'Other'])) ? $job_party->copy_type : 'other'), ['class' => 'form-control copy_recipient_type']) !!}
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-12 form-group other_type {{((array_key_exists($job_party->copy_type,['architect' => 'Architect', 'condo assoc' => 'Condo Assoc', 'Developer' => 'Developer', 'engineer' => 'Engineer', 'government agency' => 'Government Agency', 'homeowners assoc' => 'Homeowners Assoc', 'management co' => 'Management Co', 'surveying co' => 'Surveying Co', 'owner' => 'Owner', 'owner designated' => 'Owner Designated','other' => 'Other'])) ? 'hidden' : '')}}">
                                            <label>Other:</label>
                                            {!!  Form::text('other_copy_recipient_type',old('other_copy_recipient_type',((array_key_exists($job_party->copy_type,['architect' => 'Architect', 'condo assoc' => 'Condo Assoc', 'Developer' => 'Developer', 'engineer' => 'Engineer', 'government agency' => 'Government Agency', 'homeowners assoc' => 'Homeowners Assoc', 'management co' => 'Management Co', 'surveying co' => 'Surveying Co', 'owner' => 'Owner', 'owner designated' => 'Owner Designated','other' => 'Other'])) ? '' : $job_party->copy_type)), ['class' => 'form-control other_copy_recipient_type']) !!}
                                        </div>
                                    </div>
                                @endif
                                @if ($job_party->type =="bond" )
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <label>Bond Type:</label>
                                            {!! Form::select('bond_type',['agent' => 'Agent', 'company' => 'Company'], old('bond_type',$job_party->bond_type),['class' => 'form-control'])!!}
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-xs-12 form-group">
                                            <label>Bond Contract (PDF): 
                                                @if (is_null($job_party->bond_pdf))
                                                @else
                                                 <a href="{{ route('parties.downloadbond',[$job_party->job_id,$job_party->id]) }}"><i class="fa fa-file"></i> {{$job_party->bond_pdf_filename}}</a>({{ number_format($job_party->bond_pdf_filename_size,2)}}KB)
                                                @endif</label>
                                            {!!  Form::file('bond_pdf', ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-4 form-group">
                                            <label>Bond date:</label>
                                            <input name="bond_date"  value="{{ old("bond_date", (strlen($job_party->bond_date) > 0) ? date('m/d/Y', strtotime($job_party->bond_date)): '')}}" class="form-control date-picker" data-date-format="mm/dd/yy" data-date-autoclose="true" data-toggle="tooltip" data-placement="top" title="">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Bond Number:</label>
                                            {!!  Form::text('bond_bookpage_number',old('bond_bookpage_number',$job_party->bond_bookpage_number), ['class' => 'form-control']) !!}
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label>Bond Amount:</label>
                                            {!!  Form::number('bond_amount',old('bond_amount',$job_party->bond_amount), ['class' => 'form-control', 'min'=>'0' ,'step' => '0.01']) !!}
                                        </div>
                                    </div>
                                @endif
                                @if ($job_party->type =="landowner" )
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <label>Deed Number:</label>
                                            {!!  Form::text('landowner_deed_number',old('landowner_deed_number',$job_party->landowner_deed_number), ['class' => 'form-control','maxlength'=>'80']) !!}
                                        </div>
                                    </div> 
                                    
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <div class="checkbox checkbox-slider--b-flat">
                                                <label>
                                                <input name="lien_prohibition" type="checkbox" {{($job_party->landowner_lien_prohibition) ? 'checked' : ''}}><span>Lien Prohibition</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                @endif
                                
                                @if ($job_party->type =="leaseholder" )
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <label>Lease Type:</label>
                                            {!! Form::select('leaseholder_type',['Lessee' =>'Lessee', 'Lessor' => 'Lessor'], old("leaseholder_type",$job_party->leaseholder_type), ['class' => 'form-control lease-type']) !!}
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-6 form-group">
                                            <label>Lease Number:</label>
                                            <input name="leaseholder_lease_number"  value="{{ old("leaseholder_lease_number",$job_party->leaseholder_lease_number)}}" class="form-control " data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                                        </div>
                             
                                        <div class="col-md-6 form-group bookpage_number">
                                            <label>Book/Page Number:</label>
                                            {!!  Form::text('leaseholder_bookpage_number',old('leaseholder_bookpage_number',$job_party->leaseholder_bookpage_number), ['class' => 'form-control','maxlength'=>'50']) !!}
                                        </div>
                                      
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-12 form-group">
                                            <label>Lease Agreement:</label>
                                            {!!  Form::textarea('leaseholder_lease_agreement',old('leaseholder_lease_agreement',$job_party->leaseholder_lease_agreement), ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    @endif
                </div>
               {!! Form::close() !!}
       
                </div>
               
            </div>
            <!-- /.container-fluid -->
    
@endsection

@section('scripts')
<script src="{{ asset('/vendor/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/vendor/datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script>
$.fn.select2.defaults.set("theme", "bootstrap");
$('.btn-save').click(function(){
    $('.btn-save').addClass("disabled");
    $('.btn-save').css('pointer-events','none');
    $('#edit_form').submit();
    
});

$(function () {
     $(".message-box").fadeTo(6000, 500).slideUp(500, function(){
        $(".message-box").slideUp(500).remove();
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
    
  $('[data-toggle="tooltip"]').tooltip()
  $('#client_id').select2();
  $('.date-picker').datepicker();
   $(":file").filestyle();
   var hash = window.location.hash;
   if (hash.length > 0 ) {
        $('#page-wrapper').scrollTop($(hash).offset().top);
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
    
     $('#states').typeahead(null, {
      name: 'states',
      source: states
    });
    
    $('#states').focus(function () {
        states.initialize(true);
    }); 
        
    
  
})
</script>
    
@endsection