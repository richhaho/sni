
    <div class="col-xs-12">
         <div class="panel panel-default">
            <div class="panel-heading">
                Notes
            </div>
             <div class="panel-body">
                <div class="col-xs-12 col-md-4">
                    @if($xnote == "")
                        
                    
                    {!! Form::open(['route' => ['client.notes.store',$e_name,$e_id]]) !!}
                     <div class="col-xs-12 form-group">
                        <label>Note:</label>
                        {!!  Form::textarea('note','', ['class' => 'form-control']) !!}
                    </div>
                   
                     <div class="col-xs-6 col-xs-offset-6 form-group ">
                        <button class="btn btn-success pull-right note_save" type="submit"> <i class="fa fa-floppy-o"></i> Add Note</button>
                    </div>
                    {!! Form::close() !!}
                    @else
                        {!! Form::open(['route' => ['client.notes.update',$e_name,$e_id,$xnote->id], 'method'=> 'PUT', 'id'=> 'edit_note_form' . $xnote->id]) !!}
                        <div class="col-xs-12 form-group">
                           <label>Note by @if (isset($note->writer->full_name)){{ $note->writer->full_name }} @else Deleted User @endif</label>
                           <span class="pull-right text-muted"><em>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($xnote->entered_at))->diffForHumans() }}</em></span>
                           {!!  Form::textarea('note'.$xnote->id,old('note'.$xnote->id,$xnote->note_text), ['class' => 'form-control']) !!}
                       </div>
                       <div class="col-md-6 form-group">

                       
                        </div>    
                            <div class="col-xs-12 form-group ">
                           <button class="btn btn-success pull-right note_save" type="submit" form="edit_note_form{{$xnote->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
                       </div>
                       {!! Form::close() !!}
                    @endif
                </div>
                  
                    
                
                 <div class="col-xs-12 col-md-8" style="overflow-x: scroll;">
                     @include('client.notes.list', ['notes' => $notes,'e_name' => $e_name,'e_id'=> $e_id])
                     
                 </div>
               </div>
         </div>    
    </div>

