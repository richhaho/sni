    <div class="row">
                    {!! Form::open(['route' => ['client.notices.update',$work->id],'method'=> 'PUT', 'id'=> 'edit_form','autocomplete' => 'off']) !!}
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Details
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 col-md-7 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::select('job_id',$jobs_list,old("job_id",$work->job_id), ['class' => 'form-control','id'=>'job_id','disabled']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-5 form-group">
                                        <label>Type:</label>
                                        {!!  Form::select('type',$wo_types,old("type",$work->type), ['class' => 'form-control','disabled','id'=>'work_order_type']) !!}
                                    </div>
                                    
                                </div>
                                 <div class="row">
                           
                                    
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Max Mailing Date:</label>
                                        <input disabled name="due_at"  value="{{ old("due_at", (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '')}}" class="form-control date-picker" data-date-format="mm/dd/yy" data-date-autoclose="true" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                     
                                     <div class="col-xs-12 col-md-4 form-group">
                                        <label>Due Date:</label>
                                        <input disabled name="mailing_at"  value="{{ old("mailing_at", (strlen($work->mailing_at) > 0) ? date('m/d/Y', strtotime($work->mailing_at)): '')}}" class="form-control date-picker" data-date-format="mm/dd/yy" data-date-autoclose="true"  data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                     
                                
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input disabled name="is_rush" type="checkbox" {{ ($work->is_rush ==1) ? 'checked' :''}}><span>Is Rush?</span>
                                        </label>
                                    </div>
                                    </div>
                                
                                </div>
                                
                            </div>
                        </div>
                        <div class="AdditionalQuestions">
                        
                        </div>
                    </div>
                    
                    <div class="col-xs-12 text-right saveButton">
                        
                    </div>
                    {!! Form::close() !!}
                    
                    
                </div>