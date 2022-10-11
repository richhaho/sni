<li><a href="#" data-toggle="modal" data-target="#modal-job-close-{{ $id }}"><i class="fa fa-times"></i> Close Job</a></li>
</ul>
<div class="modal fade" id="modal-job-close-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Close Job</h4>
      </div>
      <div class="modal-body">
          <p>Are you sure you want to close the job:<br> {{ $client_name }}<br>
            Doing so will mean no longer being able to enter new notices on this job.
          </p>
      </div>
      <div class="modal-footer">
          
        
        {!! Form::open(['route' => ['jobs.close',$id]]) !!}
            {!! Form::hidden('_method', 'POST') !!}
            {!! Form::hidden('redirect_to', URL::full()) !!}
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-danger" type="submit"><i calss="fa fa-times"></i> Close</button>
        {!! Form::close() !!}
            
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>


    
