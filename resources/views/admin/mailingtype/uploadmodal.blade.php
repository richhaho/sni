<a href="#" data-toggle="modal" data-target="#modal-upload-csv" class="btn btn-primary"><i class="fa fa-upload"></i> Upload CSV</a>
<div class="modal fade" id="modal-upload-csv" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Upload CSV File</h4>
      </div>
      {!! Form::open(['route' => ['mailingtype.upload'], 'method'=> 'POST', 'files' => true, 'autocomplete' => 'off']) !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <h5>CSV File:</h5>
            <input type="file" name="csv" class="form-control" accept=".csv" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-success">Upload CSV</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i calss="fa fa-times"></i> Cancel</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>