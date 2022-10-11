<div class="col-xs-12 col-md-6 col-lg-6">

    {!! Form::open(['route' => ['notes.update',$e_name,$e_id,$note->id], 'method'=> 'PUT', 'id'=> 'edit_note_form' . $note->id,'autocomplete' => 'off']) !!}

 <div class="col-xs-12 form-group">
    <label>Note by {{ $note->writer->full_name }}</label>
    <span class="pull-right text-muted"><em>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($note->entered_at))->diffForHumans() }}</em></span>
    {!!  Form::textarea('note'.$note->id,old('note'.$note->id,$note->note_text), ['class' => 'form-control']) !!}
</div>
<div class="col-md-6 form-group">

    <div class="checkbox checkbox-slider--b-flat">
        <label>
        <input name="viewable" type="checkbox" {{ $note->viewable == 1 ? 'checked' : ''}}><span>Viewable by Customer</span>
        </label>
    </div>
 </div>    
{!! Form::close() !!}
 <div class="col-xs-12 form-group ">
    @component('researcher.notes.components.deletemodal')
         @slot('id') 
             {{ $note->id }}
         @endslot
         @slot('e_name') 
                {{ $e_name }}
         @endslot
         @slot('e_id') 
             {{ $e_id }}
         @endslot
     @endcomponent
    <button class="btn btn-success pull-right" type="submit" form="edit_note_form{{$note->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
</div>

</div>
