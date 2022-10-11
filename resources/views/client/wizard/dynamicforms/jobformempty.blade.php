@if (!old("county"))
<style type="text/css">
    .address_fields,.wait{
        display: none;
    }
</style>
@endif
<div class="col-sm-12 col-md-6">
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
                            {!!  Form::text('association_name',old("association_name"), ['class' => 'form-control','id'=>'association_name', 'maxlength'=>'191']) !!}
                        </div>
                        <div class="col-xs-12 col-md-6 form-group a_unit_number">
                            <label>Unit #:</label>
                            {!!  Form::text('a_unit_number',old("a_unit_number"), ['class' => 'form-control','id'=>'a_unit_number', 'maxlength'=>'191']) !!}
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
                            {!!  Form::text('mall_name',old("mall_name"), ['class' => 'form-control','id'=>'mall_name', 'maxlength'=>'191']) !!}
                        </div>
                        <div class="col-xs-12 col-md-6 form-group m_unit_number ">
                            <label>Unit #:</label>
                            {!!  Form::text('m_unit_number',old("m_unit_number"), ['class' => 'form-control','id'=>'m_unit_number', 'maxlength'=>'191']) !!}
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    <label class="requiredfiled">Job Name*:</label>
                    <input name="name"  value="{{ old("name")}}" class="form-control" data-toggle="tooltip"  data-placement="bottom"  title="Descriptive name for the Job" autocomplete="false" maxlength="200">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-12 form-group">
                    <label class="requiredfiled">County*:</label>
                    <input id="counties" name="county"  value="{{ old("county")}}" class="form-control county" maxlength="50" list='county_list' oninput="enteringCounty()">
                    <datalist id='county_list'>
                        <script>var counties=[];</script>
                        <?php $counties=['ALACHUA','BAKER','BAY','BRADFORD','BREVARD','BROWARD','CALHOUN','CHARLOTTE','CITRUS','CLAY','COLLIER','COLUMBIA','DESOTO','DIXIE','DUVAL','ESCAMBIA','FLAGLER','FRANKLIN','GADSDEN','GILCHRIST','GLADES','GULF','HAMILTON','HARDEE','HENDRY','HERNANDO','HIGHLANDS','HILLSBOROUGH','HOLMES','INDIAN RIVER','JACKSON','JEFFERSON','LAFAYETTE','LAKE','LEE','LEON','LEVY','LIBERTY','MADISON','MANATEE','MARION','MARTIN','MIAMI-DADE','MONROE','NASSAU','OKALOOSA','OKEECHOBEE','ORANGE','OSCEOLA','PALM BEACH','PASCO','PINELLAS','POLK','PUTNAM','SANTA ROSA','SARASOTA','SEMINOLE','ST. JOHNS','ST. LUCIE','SUMTER','SUWANNEE','TAYLOR','UNION','VOLUSIA','WAKULLA','WALTON','WASHINGTON']; ?>
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
                    <input name="address_corner" value="{{ old("address_corner")}}" class="form-control address_corner" data-toggle="tooltip" data-placement="bottom"  title="If you do not know the exact address you can write the closest corner address to the job location" maxlength="200" >
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
                @if(Auth::user()->client->gps_tracking)
                <div class="col-md-12 col-lg-12 form-group">
                    <label>Coordinate:</label>
                    {!!  Form::select('coordinate_id',$coordinates,old("coordinate_id"), ['class' => 'form-control','id'=>'coordinate_id']) !!}
                </div>
                @endif
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

<div class="col-sm-12 col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            Additional Information
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label class="requiredfiled">Date Started*:</label>
                    <input name="started_at"  value="{{ old("started_at")}}" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-toggle="tooltip" data-placement="bottom" title="This is the date you started the job.  If this is prefabricated work then the job date starts at the time of prefabrication.">
                </div>
                <div class="col-md-3 form-group">
                    <label>Last Day on Job:</label>
                    <input name="last_day"  value="{{ old("last_day")}}" class="form-control date-picker" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-toggle="tooltip" data-placement="bottom" title="Punch Work Will Not Extend Your Last Day">
                </div>
                 <div class="col-md-3 form-group">
                <label>Contract Amount:</label>
                {!!  Form::number('contract_amount',old("contract_amount",0), ['class' => 'form-control', 'min'=>'0','step'=>'0.01','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Written or Oral Dollar Amount, This is for your tracking only and will not be included on your Notice to Owner', 'maxlength'=>'17']) !!}
                </div>
                <div class="col-md-3 form-group">
                    <label>Interest rate:</label>
                    {!!  Form::number('interest_rate',old("contract_amount"), ['class' => 'form-control', 'min'=>'0','step'=>'0.01', 'id'=>"interest_rate", 'maxlength'=>'17']) !!}
                </div>
            </div>
           <div class="row">
           <div class="col-md-6 form-group">
                    <label>Folio Number:</label>
                    {!!  Form::text('folio_number',old("folio_number"), ['class' => 'form-control folio_number','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'This is also known as  the parcel id on the tax rolls', 'maxlength'=>'50']) !!}
                </div>
                    <div class="col-md-6 form-group">
                    <label>Job Number:</label>
                    {!!  Form::text('number',old("number"), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Your job number (if you have one) representing this job/contract', 'maxlength'=>'20']) !!}
                </div>
                <div class="col-md-6 form-group">
                    <label>NOC Number:</label>
                    {!!  Form::text('noc_number',old("noc_number"), ['class' => 'form-control','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'If you have a copy of the Notice of Commencement add Official Book and Page here - And attach copy to attachments', 'maxlength'=>'50']) !!}
                </div>
                 <div class="col-md-6 form-group pnumber-group">
                    <label>Project Number:</label>
                    {!!  Form::text('project_number',old("project_number"), ['class' => 'form-control','id' => 'project_number', 'maxlength'=>'50']) !!}
                </div>
           </div>
            <div class="row">
            <div class="col-md-12 form-group">
                <label>Job Legal Description:</label>
                {!!  Form::textarea('legal_description',old("legal_description"), ['class' => 'form-control legal_description','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'This can be found in Tax Rolls or Contract Info']) !!}
            </div>
            </div>
            <div class="row">
            <div class="col-md-12 form-group">
                <label>Default Materials:</label>
                {!!  Form::textarea('default_materials',old("default_materials",Auth::user()->client->default_materials), ['class' => 'form-control','id' => 'default_materilas','data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Will prefill with ']) !!}
            </div>
            </div>
             
            
        </div>
    </div>
</div>

<script type="text/javascript">
/*
   $('[data-toggle="tooltip"]').tooltip();
   $('input').click(function(){
    
    $('.btn-success').removeClass("disabled");
    $('.btn-success').css('pointer-events','auto');
  });
  $('input').keydown(function(){
      $('.btn-success').removeClass("disabled");
      $('.btn-success').css('pointer-events','auto');
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

 */ 
</script>