
<div class="modal fade" id="modal-noc-delete-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete this job NOC.</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete the job NOC:<br></p>
      </div>
      <div class="modal-footer">
        {!! Form::open(['route' => ['jobnocs.destroy',$job_id,$id]]) !!}
            {!! Form::hidden('_method', 'DELETE') !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Delete</button>
        {!! Form::close() !!}
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
