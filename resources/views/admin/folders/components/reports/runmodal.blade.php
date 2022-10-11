<a href="#" data-toggle="modal" data-target="#modal-report-run-{{$id}}" class="btn btn-primary pull-right btn-xs" style="margin-right:5px"><i class="fa fa-play"></i> Run</a>
<div class="modal fade" id="modal-report-run-{{$id}}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Run report.</h4>
      </div>
      {!! Form::open(['route' => ['reports.run', $id]]) !!}
      {!! Form::hidden('_method', 'POST') !!}
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 form-group">
            <label><h5>Folder: <strong>{{$folder_name}}</strong></h5></label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Report Name: </h5> {{$name}}</label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Query: </h5> {{$sql}}</label>
          </div>
          <div class="col-xs-12 form-group">
            <label><h5>Client: </h5></label>
            <?php
              $client_list = [];
              if ($client_type=='both') {
                $client_list = \App\Client::where('deleted_at', null)->get()->pluck('company_name', 'id')->toArray();
              } elseif ($client_type=='self') {
                $client_list = \App\Client::where('deleted_at', null)->where('service', 'self')->get()->pluck('company_name', 'id')->toArray();
              } else {
                $client_list = \App\Client::where('deleted_at', null)->where(function($q) {
                  $q->where('service', null)->orwhere('service', 'full');
                })->get()->pluck('company_name', 'id')->toArray();
              }
            ?>
            {!!  Form::select('client_id',$client_list,'', ['class' => 'form-control', 'reqiured'=>true]) !!}
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-success">Run Report</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i calss="fa fa-times"></i> Cancel</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>