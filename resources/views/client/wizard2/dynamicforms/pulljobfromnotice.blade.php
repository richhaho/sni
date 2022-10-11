<div class="modal fade" id="modal-pulljobfromnotice" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pull Job from Notice</h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <label>Job Number</label>
              <input type="text" name="job_number" value="{{$job_number}}" class="form-control job_number">
            </div>
            <div class="col-xs-12 col-md-12">
              <label>Job Secret Key</label>
              <input type="text" name="job_secret" value="{{$secret_key}}" class="form-control job_secret">
            </div>
            <div class="col-xs-12 col-md-12">
              <br>
              <p class="text-danger hidden pull_job_status">* No job found.</p>
            </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
          <button class="btn btn-warning btn-pull-job-from-notice" type="button">Pull Job from Notice </button>
      </div>
    </div>
  </div>
</div>
