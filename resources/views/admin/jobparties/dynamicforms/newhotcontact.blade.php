<div class="row">
    <div class="col-xs-12">
       <div class="panel panel-default">
           <div class="panel-heading">
               Contact Info
           </div>
           <div class="panel-body">
               <div class="row new-company" >
               <div class="col-xs-12 form-group">
                   <label>New Company Name:</label>
                   <div class="input-group">
                   <input name="firm_name"  value="{{ old("firm_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                   <span class="input-group-btn">
                        <button id="choose-company-button" class="btn btn-warning" type="button">Choose...</button>
                   </span>
                   </div>
               </div>
               </div>
              {!! Form::hidden('newhotcontact','true') !!}
              <div class="row choose-company">
               <div class="col-xs-12 form-group">
                   <label>Existing Company:</label>
                    <div class="input-group">
                   {!!  Form::select('entity_id',$hotentities,old("entity_id"), ['class' => 'form-control entity_id']) !!}
                    <span class="input-group-btn">
                        <button id="new-company-button" class="btn btn-warning" type="button">New Company</button>
                   </span>
                   </div>
               </div>
               </div>
               <div class="row">
               <div class="col-xs-12 form-group">
                   <label>Associate First Name:</label>
                   <input name="first_name"  value="{{ old("first_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""   maxlength="50">
               </div>
               </div>
               <div class="row">
               <div class="col-xs-12 form-group">
                   <label>Associate Last Name:</label>
                   <input name="last_name" value="{{ old("last_name")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="50">
               </div>
               </div>
               <div class="row">
               <div class="col-xs-12 form-group">
                   <label>Email:</label>
                   <input name="email" value="{{ old("email")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="100">
               </div>
               </div>

               <div class="row">
               <!--<div class="col-xs-12 form-group">
                   <label>Gender:</label>
                   {!!  Form::select('gender',$gender,old("gender"), ['class' => 'form-control','']) !!}
               </div>-->
               </div>
               <div class="row">
               <div class="col-lg-12 form-group">
                   <label>Phone:</label>
                   <input name="phone" value="{{ old("phone")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="20">
               </div>

               <div class=" col-lg-12 form-group">
                   <label>Mobile:</label>
                   <input name="mobile" value="{{ old("mobile")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="20">
               </div>
               <div class="col-lg-12 form-group">
                   <label>Fax:</label>
                   <input name="fax" value="{{ old("fax")}}" class="form-control" data-toggle="tooltip" data-placement="top" title=""  maxlength="20">
               </div>
               </div>
           </div>
       </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Address
            </div>
            <div class="panel-body">
                <div class="row">
                <div class="col-xs-12 form-group">
                    <label>Street Address:</label>
                    <input name="address_1" value="{{ old("address_1")}}" autocomplete="off" placeholder="Street and number"  class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                </div>
                </div>
                <div class="row">
                <div class="col-xs-12 form-group">
                    <input name="address_2" value="{{ old("address_2")}}" autocomplete="off" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="150">
                </div>
                </div>
                <div class="row">
                
                <div class="col-md-12 form-group hidden">
                    <label>Country:</label>
                    <input id="countries" value="{{ old("country",'USA')}}" autocomplete="off" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off" maxlength="50">
                </div>
               
                <div class="col-md-12 form-group">
                    <label>State / Province / Region:</label>
                    <input id="states" value="{{ old("state",'FL')}}" autocomplete="off" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off" maxlength="50">
                </div>
                </div>

                <div class="row">
                <div class="col-md-12  form-group">
                    <label>City:</label>
                    <input name="city"  value="{{ old("city")}}"  autocomplete="off" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="100">
                </div>

                <div class="col-md-12  form-group">
                    <label>Zip code:</label>
                    <input name="zip"  value="{{ old("zip")}}" autocomplete="off" class="form-control" data-toggle="tooltip" data-placement="top" title="" maxlength="50">
                </div>
                </div>

            </div>
        </div>
    </div>               <!-- /.col-lg-12 -->
</div>
                <!-- /.row -->
