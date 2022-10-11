
<div class="modal fade" id="modal-noc-edit-{{ $id }}" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit NOC</h4>
      </div>
      {!! Form::open(['route' => ['client.jobnocs.update', $job_id, $id],'files' => true]) !!}
      {!! Form::hidden('_method', 'PUT') !!}
      <div class="modal-body row">
          <div class="col-xs-12 form-group">
            <label>NOC#:</label>
            <input name="noc_number" value="{{$noc_number}}" required type="text" class="form-control">
          </div>
          <div class="col-xs-12 form-group">
            <label>NOC Recording Date:</label>
            <input name="recorded_at" value="{{date('m/d/Y', strtotime($recorded_at))}}" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
          </div>
          <div class="col-xs-12 form-group">
            <label>NOC Notes:</label>
            <textarea name="noc_notes" rows="5" class="form-control">{{$noc_notes}}</textarea>
          </div>
          <div class="col-xs-12 form-group">
            <label>Copy of NOC (PDF):</label>
            {!!  Form::file('copy_noc','', ['class' => 'form-control']) !!}
          </div>
          <div class="col-xs-12 form-group">
            <label>Expiration Date:</label>
            <input name="expired_at" value="{{date('m/d/Y', strtotime($expired_at))}}" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
          </div>
          <div class="col-xs-12 form-group">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-info pull-right" type="submit"><i calss="fa fa-floppy-o"> Save</i></button>&nbsp;&nbsp;
          </div>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
