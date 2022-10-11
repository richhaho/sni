    <?php $admin_users = \App\User::where('status',1)->isRole(['admin','researcher'])->get()->pluck('full_name', 'id')->prepend('','')->toArray(); ?>
    <div class="col-xs-12">
         <div class="panel panel-default">
            <div class="panel-heading">
                Notes
            </div>
             <div class="panel-body">
                <div class="col-lg-4  col-md-12  col-sm-12 col-xs-12">
                    @if($xnote == "")
                        
                    
                    {!! Form::open(['route' => ['notes.store',$e_name,$e_id]]) !!}
                     <div class="col-lg-12 form-group">
                        <label>Note:</label>
                        {!!  Form::textarea('note','', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-lg-12 form-group">
                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="viewable" type="checkbox"><span>Viewable by Customer</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-12 form-group ">
                        <label>Notify:</label>
                        {!! Form::select('notify_admin_researcher', $admin_users,'',['class' => 'form-control'])!!}
                    </div>
                    <div class="col-lg-12 form-group ">
                        <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Add Note</button>
                    </div>
                    {!! Form::close() !!}
                    @else
                        {!! Form::open(['route' => ['notes.update',$e_name,$e_id,$xnote->id], 'method'=> 'PUT', 'id'=> 'edit_note_form' . $xnote->id]) !!}
                        <div class="col-lg-12 form-group">
                           <label>Note by @if (isset($note->writer->full_name)){{ $note->writer->full_name }} @else Deleted User @endif</label>
                           <span class="pull-right text-muted"><em>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($xnote->entered_at))->diffForHumans() }}</em></span>
                           {!!  Form::textarea('note'.$xnote->id,old('note'.$xnote->id,$xnote->note_text), ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-lg-12 form-group">
                           <div class="checkbox checkbox-slider--b-flat">
                               <label>
                               <input name="viewable" type="checkbox" {{ $xnote->viewable == 1 ? 'checked' : ''}}><span>Viewable by Customer</span>
                               </label>
                           </div>
                        </div>    
                        <div class="col-lg-12 form-group ">
                            <label>Notify:</label>
                            {!! Form::select('notify_admin_researcher', $admin_users,$xnote->notify_admin_researcher,['class' => 'form-control'])!!}
                        </div>
                        <div class="col-lg-12 form-group ">
                           <button class="btn btn-success pull-right" type="submit" form="edit_note_form{{$xnote->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
                        </div>
                       {!! Form::close() !!}
                    @endif
                </div>
                  
                    
                
                 <div class="col-lg-8  col-md-12  col-sm-12 col-xs-12">
                     @include('admin.notes.list', ['notes' => $notes,'e_name' => $e_name,'e_id'=> $e_id])
                     
                 </div>
               </div>
         </div>    
    </div>

