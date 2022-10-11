
<div class="modal fade" id="modal-client-delete-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Client</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to delete the client:<br> {{ $client_name }}</p>
      </div>
      <div class="modal-footer">
          
        
        {!! Form::open(['route' => ['clients.destroy',$id]]) !!}
            {!! Form::hidden('_method', 'DELETE') !!}
            {!! Form::hidden('redirect_to', URL::full()) !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Delete</button>
        {!! Form::close() !!}
            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<a class="btn btn-danger" href="#" data-toggle="modal" data-target="#modal-client-delete-{{ $id }}"><i class="fa fa-times"></i> Delete</a>