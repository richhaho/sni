<div class="modal fade" id="modal-workorder-cancel-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Cancel Work Order</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to cancel this work order #{{ $work_number }}?  Any generated documents will be deleted.</p>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;</button>&nbsp;&nbsp;
            <button class="btn btn-success" type="button" onclick="cancelWorkorder({{$id}})"><i calss="fa fa-times"></i>&nbsp;&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
    
    