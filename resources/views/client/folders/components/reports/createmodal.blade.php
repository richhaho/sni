<a href="#" data-toggle="modal" data-target="#modal-folder-{{$folder_id}}-report-create" class="btn btn-success pull-right btn-xs"><i class="fa fa-plus"></i> Add New Report</a>
<div class="modal fade" id="modal-folder-{{$folder_id}}-report-create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create new report.</h4>
      </div>
      {!! Form::open(['route' => ['client.reports.store']]) !!}
      {!! Form::hidden('_method', 'POST') !!}
      {!! Form::hidden('folder_id', $folder_id) !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>Folder: <strong>{{$folder_name}}</strong></h5></label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Report Name: </h5></label>
            <input type="text" name="name" class="form-control" value="" required>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Client Type: </h5></label>
            {!!  Form::select('client_type',['full'=>'Full-Service', 'self'=>'Self-Service', 'both'=>'Both (Full & Self)'],'both', ['class' => 'form-control']) !!}
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Report Query: </h5></label>
            <textarea name="sql" class="noucase form-control" rows="6" required></textarea>
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