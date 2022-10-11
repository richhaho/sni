

<div class="modal fade" id="modal-qrcode-{{$job_id}}" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Scan QR code to request get shared this job</h4>
      </div>
      <div class="modal-body">
          <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
              ->size(400)
              ->generate(url('/jobs/'.$job_id.'/share_request_from/'.$user_id))) !!}">
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>


    
