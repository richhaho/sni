<a href="#" data-toggle="modal" data-target="#modal-folder-delete-{{ $id }}" class="btn btn-danger btn-xs pull-right" style="margin-right: 5px;"><i class="fa fa-times"></i> Delete</a>
<div class="modal fade" id="modal-folder-delete-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete this folder.</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete the folder:<br></p>
      </div>
      <div class="modal-footer">
      {!! Form::open(['route' => ['folders.destroy', $id]]) !!}
      {!! Form::hidden('_method', 'DELETE') !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button type="submit" class="btn btn-danger"><i calss="fa fa-times"></i> Delete</button>&nbsp;&nbsp;
      {!! Form::close() !!}
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

    
      
    
    