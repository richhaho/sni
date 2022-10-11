<a href="#" data-toggle="modal" data-target="#modal-folder-edit-{{$id}}" class="btn btn-warning pull-right btn-xs" style="margin-right: 5px;"><i class="fa fa-edit"></i> Edit</a>
<div class="modal fade" id="modal-folder-edit-{{$id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Folder.</h4>
      </div>
      {!! Form::open(['route' => ['folders.update', $id]]) !!}
      {!! Form::hidden('_method', 'PUT') !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>Folder Name: </h5></label>
            <input type="text" name="name" class="form-control" value="{{$name}}" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i calss="fa fa-times"></i> Cancel</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
