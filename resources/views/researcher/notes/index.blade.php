
    <div class="col-xs-12">
         <div class="panel panel-default">
            <div class="panel-heading">
                Notes
            </div>
             <div class="panel-body">
                <div class="col-xs-12 col-md-4">
                    @if($xnote == "")
                        
                    
                    {!! Form::open(['route' => ['notes.store',$e_name,$e_id]]) !!}
                     <div class="col-xs-12 form-group">
                        <label>Note:</label>
                        {!!  Form::textarea('note','', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-6 form-group">
                        
                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="viewable" type="checkbox"><span>Viewable by Customer</span>
                            </label>
                        </div>
                     </div>    
                     <div class="col-xs-6 form-group ">
                        <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Add Note</button>
                    </div>
                    {!! Form::close() !!}
                    @else
                        {!! Form::open(['route' => ['notes.update',$e_name,$e_id,$xnote->id], 'method'=> 'PUT', 'id'=> 'edit_note_form' . $xnote->id]) !!}
                        <div class="col-xs-12 form-group">
                           <label>Note by {{ $xnote->writer->full_name }}</label>
                           <span class="pull-right text-muted"><em>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($xnote->entered_at))->diffForHumans() }}</em></span>
                           {!!  Form::textarea('note'.$xnote->id,old('note'.$xnote->id,$xnote->note_text), ['class' => 'form-control']) !!}
                       </div>
                       <div class="col-md-6 form-group">

                           <div class="checkbox checkbox-slider--b-flat">
                               <label>
                               <input name="viewable" type="checkbox" {{ $xnote->viewable == 1 ? 'checked' : ''}}><span>Viewable by Customer</span>
                               </label>
                           </div>
                        </div>    
                            <div class="col-xs-12 form-group ">
                           <button class="btn btn-success pull-right" type="submit" form="edit_note_form{{$xnote->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
                       </div>
                       {!! Form::close() !!}
                    @endif
                </div>
                  
                    
                
                 <div class="col-md-8">
                     @include('researcher.notes.list', ['notes' => $notes,'e_name' => $e_name,'e_id'=> $e_id])
                     
                 </div>
               </div>
         </div>    
    </div>

