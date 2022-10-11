   {!! Form::open(['route' => ['jobs.update',$job->id], 'method'=> 'PUT', 'id'=> 'edit_form','autocomplete' => 'off']) !!}
                {{ Form::hidden('redirects_to', Session::get('backUrl')) }}
                {{ Form::hidden('workorder', $work_order) }}
                
                <div class="row">
                       
                           
                    <div class="col-xs-11">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Client Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    {!!  Form::select('client_id',$clients,old("client_id",$job->client_id), ['class' => 'form-control','id'=>'client_id']) !!}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-1 text-center">
                            <button class="btn btn-success btn-block " type="submit" form="edit_form"> <i class="fa fa-floppy-o"></i> Save</button>
                        </div>

                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Job Site Info
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                   
                                    <div class="col-xs-12 col-md-12 form-group">
                                        <label>Job Type:</label>
                                        {!!  Form::select('type',$job_types,old("type",$job->type), ['class' => 'form-control','id'=>'job_type']) !!}
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
                                        {!!  Form::select('private_type',['residential' =>'Residential', 'commercial' => 'Commercial'],old("private_type",$job->private_type), ['class' => 'form-control','id'=>'private_type']) !!}
                                    </div>
                                    <div class="ptype-residential">
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Is this a Condo?:</label>
                                        {!!  Form::select('is_condo',['0' =>'No', '1' => 'Yes'],old("is_condo",$job->is_condo), ['class' => 'form-control','id'=>'is_condo']) !!}
                                    </div>
                                        <div class="is_condo">
                                            <div class="col-xs-12 col-md-6 form-group association_name">
                                                <label>Association Name:</label>
                                                {!!  Form::text('association_name',old("association_name",$job->association_name), ['class' => 'form-control','id'=>'association_name']) !!}
                                            </div>
                                            <div class="col-xs-12 col-md-6 form-group a_unit_number">
                                                <label>Unit #:</label>
                                                {!!  Form::text('a_unit_number',old("a_unit_number",$job->a_unit_number), ['class' => 'form-control','id'=>'a_unit_number']) !!}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="ptype-commercial">
                                    <div class="col-xs-12 col-md-12 form-group">
                                        <label>Is this a Mall Unit?:</label>
                                        {!!  Form::select('is_mall_unit',['0' =>'No', '1' => 'Yes'],old("is_mall_unit",$job->is_mall_unit), ['class' => 'form-control','id'=>'is_mall_unit']) !!}
                                    </div>
                                        <div class="is_mall_unit_0">
                                             <div class="col-xs-12 col-md-12 form-group">
                                                <label>Is this a Tenant?:</label>
                                                {!!  Form::select('is_tenant',['0' =>'No', '1' => 'Yes'],old("is_tenant",$job->is_tenant), ['class' => 'form-control','id'=>'is_tenant']) !!}
                                            </div>
                                        </div>
                                        <div class="is_mall_unit_1">
                                            <div class="col-xs-12 col-md-6 form-group mall_name">
                                                <label>Mall Name:</label>
                                                {!!  Form::text('mall_name',old("mall_name",$job->mall_name), ['class' => 'form-control','id'=>'mall_name']) !!}
                                            </div>
                                            <div class="col-xs-12 col-md-6 form-group m_unit_number ">
                                                <label>Unit #:</label>
                                                {!!  Form::text('m_unit_number',old("m_unit_number",$job->m_unit_number), ['class' => 'form-control','id'=>'m_unit_number']) !!}
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class="row">
                                    <div class="col-xs-12 form-group">
                                        <label>Job Name:</label>
                                        <input name="name"  value="{{ old("name",$job->name)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                 <div class="row">
                                     <div class="col-xs-12 col-md-6 form-group">
                                        <label>Job Status:</label>
                                        {!!  Form::select('status',$job_statuses,old("status",$job->status), ['class' => 'form-control']) !!}
                                    </div>
                                <div class="col-xs-12 col-md-6 form-group">
                                    <label>Address Source:</label>
                                    {!!  Form::select('address_source',$address_sources,old("address_source", array_search($job->address_source,$address_sources)), ['class' => 'form-control']) !!}
                                   
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Street Address:</label>
                                    <input name="address_1" value="{{ old("address_1",$job->address_1)}}" placeholder="Street and number" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <input name="address_2" value="{{ old("address_2",$job->address_2)}}" placeholder="Apartment, suite, unit, building, floor, etc." class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label>Address Corner:</label>
                                    <input name="address_corner" value="{{ old("address_corner",$job->address_corner)}}" placeholder="" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                </div>
                                <div class="row ">
                                <div class="col-md-12 col-lg-4 form-group hidden">
                                    <label>Country:</label>
                                    <input id="countries" value="{{ old("country",$job->country)}}" name="country" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="" autocomplete="off">
                                </div>
                             <div class="col-md-6 col-lg-6 form-group">
                                    <label>City:</label>
                                    <input name="city"  value="{{ old("city",$job->city)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-6 col-lg-6 form-group">
                                    <label>State / Province / Region:</label>
                                    <input id="states" value="{{ old("state",$job->state)}}" name="state" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title=""  autocomplete="off">
                                </div>
                                </div>
                                
                                <div class="row">
                                     <div class="col-md-12 col-lg-6 form-group">
                                    <label>Zip code:</label>
                                    <input name="zip"  value="{{ old("zip",$job->zip)}}" class="form-control" data-toggle="tooltip" data-placement="top" title="">
                                </div>
                                <div class="col-md-12 col-lg-6 form-group">
                                    <label>County:</label>
                                    <input id="counties" name="county"  value="{{ old("county",$job->county)}}" class="form-control typeahead" data-toggle="tooltip" data-placement="top" title="">
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
                                    <div class="col-md-6 form-group">
                                        <label>Date Started:</label>
                                        <input name="started_at"  value="{{ old("started_at", (strlen($job->started_at) > 0) ? date('m/d/Y', strtotime($job->started_at)): '')}}"  data-date-autoclose="true" class="form-control date-picker"  data-date-format ="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Last Day on Job:</label>
                                        <input name="last_day"  value="{{ old("last_day", (strlen($job->last_day) > 0) ? date('m/d/Y', strtotime($job->last_day)): '')}}" data-date-autoclose="true"  class="form-control date-picker" data-date-format="mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Folio Number:</label>
                                        {!!  Form::text('folio_number',old("folio_number", $job->folio_number), ['class' => 'form-control']) !!}
                                    </div>
                                        <div class="col-md-3 form-group">
                                        <label>Job Number:</label>
                                        {!!  Form::text('number',old("number", $job->number), ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>NOC Number:</label>
                                        {!!  Form::text('noc_number',old("noc_number",$job->noc_number), ['class' => 'form-control']) !!}
                                    </div>
                                     <div class="col-md-3 form-group pnumber-group">
                                        <label>Project Number:</label>
                                        {!!  Form::text('project_number',old("project_number",$job->project_number), ['class' => 'form-control','id' => 'project_number']) !!}
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-6 form-group">
                                        <label>Contract Amount:</label>
                                        {!!  Form::number('contract_amount',old("contract_amount",$job->contract_amount), ['class' => 'form-control', 'step'=>'0.01','min'=>'0']) !!}
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Interest rate applicable:</label>
                                        {!!  Form::number('interest_rate',old("contract_amount",$job->interest_rate), ['class' => 'form-control', 'min'=>'0','step'=>'0.01', 'id'=>"interest_rate"]) !!}
                                    </div>
                                </div>
                                
                                <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Default Materials:</label>
                                    {!!  Form::textarea('default_materials',old("default_materials",$job->default_materials), ['class' => 'form-control','id' =>'default_materilas']) !!}
                                </div>
                                </div>
                                 <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Legal Descriptions:</label> <a class="cleanup" data-id="legal-description" href="#">Clean Up</a>
                                    {!!  Form::textarea('legal_description',old("legal_description",$job->legal_description), ['class' => 'form-control','id'=> 'legal-description']) !!}
                                </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-right">
                        <button class="btn btn-success " type="submit" form="edit_form"> <i class="fa fa-floppy-o"></i> Save</button>
                    </div>
                </div>
               {!! Form::close() !!}