<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-instruction-delete-{{ $id }}"><i class="fa fa-times"></i> Delete</button>
 

<div class="modal fade" id="modal-instruction-delete-{{$id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-left">Delete Todo Instruction</h4>
      </div>
      <div class="modal-body text-left">
          <p>Are you sure you want to delete the ToDo Instruction?</p>
      </div>
      <div class="modal-footer">
          
        
       {!! Form::open(['url' => route('client.notices.todo.instruction.destroy',$id)]) !!}
        {!! Form::hidden('_method', 'DELETE') !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Delete</button>
        {!! Form::close() !!}
            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>