<a href="#" data-toggle="modal" data-target="#modal-site-create" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add New Site</a></li>
<div class="modal fade" id="modal-site-create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create new site.</h4>
      </div>
      {!! Form::open(['route' => ['sites.store']]) !!}
      {!! Form::hidden('_method', 'POST') !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>County Name: </h5></label>
            <input type="text" name="county" class="form-control" value="" required>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Site Name: </h5></label>
            <input type="text" name="name" class="form-control" value="" required>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Site URL: </h5></label>
            <input type="text" name="url" class="noucase form-control" value="" required>
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

    
      
    
    