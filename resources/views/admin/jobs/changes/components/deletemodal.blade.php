<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-jobchange-delete-{{ $id }}"><i class="fa fa-times"></i> Delete</button>


<div class="modal fade" id="modal-jobchange-delete-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Cahnge Order</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete this Change Order</p>
      </div>
      <div class="modal-footer">
          
        
        {!! Form::open(['route' => ['jobchanges.destroy',$job_id,$id],'id'=> 'delete_change_form_'.$id]) !!}
            {!! Form::hidden('_method', 'DELETE') !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit" form="delete_change_form_{{$id}}"><i calss="fa fa-times"></i> Delete</button>
        {!! Form::close() !!}
            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>