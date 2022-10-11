
<div class="modal fade" id="modal-contract-tracker-edit-{{ $id }}" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Contract Tracker</h4>
      </div>
      {!! Form::open(['route' => ['contract_trackers.update', $id],'files' => true]) !!}
      {!! Form::hidden('_method', 'PUT') !!}
      <div class="modal-body row">
          <div class="col-xs-12 form-group">
            <?php
              $clients = \App\Client::get()->pluck('company_name', 'id')->prepend('', '');
            ?>
            <label>Client:</label>
            {!! Form::select('client_id',$clients, $client_id,['class'=>'form-control','id'=>'client_id'])!!}
          </div>
          <div class="col-xs-12 form-group">
            <label>Name:</label>
            <input name="name" value="{{$name}}" required type="text" class="form-control">
          </div>
          <div class="col-xs-12 form-group">
            <label>Start Date:</label>
            <input name="start_date" required value="{{date('m/d/Y', strtotime($start_date))}}" data-date-autoclose="true" class="form-control date-picker" data-date-format = "mm/dd/yyyy" data-toggle="tooltip" data-placement="top" title="">
          </div>
          <div class="col-xs-12 form-group">
            <label>Contract:</label>
            {!!  Form::file('contract_file','', ['class' => 'form-control']) !!}
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
