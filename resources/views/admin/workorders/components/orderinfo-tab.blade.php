
  <div class="row">
                    {!! Form::open(['route' => ['workorders.update',$work->id],'method'=> 'PUT', 'id'=> 'edit_form','autocomplete' => 'off']) !!}
             
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Job Name:</label>
                                        {!!  Form::select('job_id',$jobs_list,old("job_id",$work->job_id), ['class' => 'form-control','id'=>'job_id']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Job Number:</label>
                                        {{ Form::text('job_number',$work->job->number,['class'=>'form-control','disabled' => true]) }}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Job Contract Amount:</label>
                                       
                                        {{ Form::text('job_contract_amount','$ '.number_format($work->job->contract_amount,2),['class'=>'form-control','disabled' => true]) }}
                                    </div>
                                </div>
                                 <div class="row">
                                     <div class="col-xs-12 col-md-4 form-group">
                                        <label>Type:</label>
                                        {!!  Form::select('type',$wo_types,old("type",$work->type), ['class' => 'form-control','id'=>'work_order_type']) !!}
                                    </div>
                                   <div class="col-xs-12 col-md-4 form-group">
                                        <label>Status:</label>
                                        {!!  Form::select('status',$statuses,old("status",$work->status), ['class' => 'form-control']) !!}
                                    </div>
                                     <div class="col-xs-12 col-md-4 form-group">
                                        <label>&nbsp;</label>
                                    <div class="checkbox checkbox-slider--b-flat">
                                        <label>
                                            <input name="is_rush" id="is_rush" type="checkbox" {{ ($work->is_rush ==1) ? 'checked' :''}}><span>Is Rush?</span>
                                        </label>
                                    </div>
                                    </div>
                                   
                                 </div>
                                <div class="row">  
                                    <div class="col-xs-12 col-md-6 form-group">
                                        <label>Max Mailing Date:</label>
                                        <input name="due_at"  value="{{ old("due_at", (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '')}}"  data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                     
                                     <div class="col-xs-12 col-md-6 form-group">
                                        <label>Due Date:</label>
                                        <input name="mailing_at"  value="{{ old("mailing_at", (strlen($work->mailing_at) > 0) ? date('m/d/Y', strtotime($work->mailing_at)): '')}}"  data-date-format = "mm/dd/yyyy" data-date-autoclose="true" class="form-control date-picker" data-toggle="tooltip" data-placement="top" title="">
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Responsible User:</label>
                                        {!!  Form::select('responsible_user',$users,$work->responsible_user, ['class' => 'form-control','id'=>'responsible_user']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Manager:</label>
                                        {!!  Form::select('manager',$admin_users,$work->manager, ['class' => 'form-control','id'=>'manager']) !!}
                                    </div>
                                    <div class="col-xs-12 col-md-4 form-group">
                                        <label>Researcher:</label>
                                        {!!  Form::select('researcher',$admin_users,$work->researcher, ['class' => 'form-control','id'=>'researcher']) !!}
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-xs-12">
                                <div class="row">
                                  <br><br>
                                  <div class="AdditionalQuestions"></div>
                                  <table class="table lines">
                                  
                                  </table>
                                </div>
                            </div>
              
                    
                    <div class="pull-right">
                        <button class="btn btn-success " type="submit" form="edit_form"> <i class="fa fa-floppy-o"></i> Save</button>
                        
                    </div>
                    {!! Form::close() !!}
                    
                    
                </div>
               