<a href="#" data-toggle="modal" data-target="#modal-site-delete-{{ $id }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i> Delete</a></li>
<div class="modal fade" id="modal-site-delete-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete this site.</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete the site:<br></p>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <a href="{{route('sites.destroy',$id)}}" class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Delete</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

    
      
    
    