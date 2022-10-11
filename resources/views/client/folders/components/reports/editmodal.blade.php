<a href="#" data-toggle="modal" data-target="#modal-report-edit-{{$id}}" class="btn btn-warning pull-right btn-xs" style="margin-right:5px"><i class="fa fa-edit"></i> Edit</a>
<div class="modal fade" id="modal-report-edit-{{$id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create new report.</h4>
      </div>
      {!! Form::open(['route' => ['client.reports.update', $id]]) !!}
      {!! Form::hidden('_method', 'PUT') !!}
      {!! Form::hidden('folder_id', $folder_id) !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>Folder: <strong>{{$folder_name}}</strong></h5></label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Report Name: </h5></label>
            <input type="text" name="name" class="form-control" value="{{$name}}" required>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Client Type: </h5></label>
            {!!  Form::select('client_type',['full'=>'Full-Service', 'self'=>'Self-Service', 'both'=>'Both (Full & Self)'],$client_type, ['class' => 'form-control']) !!}
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Report Query: </h5></label>
            <textarea name="sql" class="noucase form-control" rows="6" required>{{$sql}}</textarea>
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