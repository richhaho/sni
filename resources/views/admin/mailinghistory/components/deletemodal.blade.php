
<div class="modal fade" id="modal-resend-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Resend Mail </h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to RESEND this mail</p>
      </div>
      <div class="modal-footer">
          
        
        {!! Form::open(['route' => ['mailinghistory.resend',$id]]) !!}
            
            <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-success btn-resend" type="submit"><i calss="fa fa-times"></i> Resend</button>
        {!! Form::close() !!}

            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<a href="#" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#modal-resend-{{ $id }}"><i class="fa fa-times"></i> Resend</a>
