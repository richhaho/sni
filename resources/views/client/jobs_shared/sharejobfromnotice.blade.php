<div class="modal fade" id="modal-sharejobfromnotice" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Share Job from Notice</h4>
      </div>
      {!! Form::open(['route' => ['client.jobs_shared.request.fromNotice']]) !!}
      <div class="modal-body">
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <label>Job Number</label>
              <input type="text" name="number" required class="form-control job_number">
            </div>
            <div class="col-xs-12 col-md-12">
              <label>Job Secret Key</label>
              <input type="text" name="secret" required class="form-control job_secret">
            </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
          <button class="btn btn-success btn-share-job-from-notice" type="submit">Share Job from Notice </button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
