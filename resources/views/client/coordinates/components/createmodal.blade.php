<div class="modal fade" id="modal-coordinate-create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add new coordinates</h4>
      </div>
      {!! Form::open(['route' => ['client.coordinates.store']]) !!}
      <div class="modal-body">
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <label>Coordinates Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-xs-12 col-md-12">
              <p class="text-danger detect_status"></p>
            </div>
            <div class="col-xs-12 col-md-6">
              <label>Latitude</label>
              <input type="text" name="lat" class="form-control coordinate_x" required>
            </div>
            <div class="col-xs-12 col-md-6">
              <label>Longitidue</label>
              <input type="text" name="lng" class="form-control coordinate_y" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
          <button class="btn btn-success" type="submit"><i calss="fa fa-save"></i> Save Coordinates</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
