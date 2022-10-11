

<div class="modal fade" id="modal-job-copy-{{$id}}" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Copy Job/Contract</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to copy this Job/Contract?   
            
          </p>
      </div>
      <div class="modal-footer">
          
        
        {!! Form::open(['route' => ['client.jobs.copy',$id]]) !!}
            {!! Form::hidden('_method', 'GET') !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Copy</button>
        {!! Form::close() !!}
            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>


    
