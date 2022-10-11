
<div class="modal fade" id="modal-tracking-number-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tracking Number</h4>
      </div>
      <div class="modal-body">
           <input value="{{$tracking_number}}" type="text" class="form-control tracking_number">
           <input value="{{$id}}" type="hidden" class="form-control tracking_number_id">
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-success btn-add-tracking-number" type="button"> Save</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-tracking-number-{{ $id }}"> Add Tracking Number</a>