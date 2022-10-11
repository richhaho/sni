
<div class="modal fade" id="modal-reminder-create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">New Reminder.</h4>
      </div>
      {!! Form::open(['route' => ['jobreminders.store', $job_id]]) !!}
      {!! Form::hidden('_method', 'POST') !!}
      <div class="modal-body row">
          <div class="col-xs-12 form-group">
            <label>Remind:</label>
            <?php
              $job=\App\Job::where('id', $job_id)->first();
              $users = $job->client->users->where('deleted_at', null)->pluck('email', 'email')->toArray();
            ?>
            {!! Form::select('email_select',$users, [] ,['class' => 'multi-select-email form-control', 'multiple'=>'multiple', 'required'=>'required'])!!}
            <input type="hidden" name="emails" class="email-content hidden">
            <p class="emails-label"></p>
            <p class="text-danger reminder-email-required">* Reminder email required. Please select one or more emails.</p>
          </div>
          <div class="col-xs-12 form-group">
            <label>To:</label>
            <textarea name="note" rows="5" class="form-control" required></textarea>
          </div>
          <div class="col-xs-12 form-group">
            <label>On:</label>
            <input name="date" value="" required data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
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
