
<div class="modal fade" id="modal-template-upload-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> Upload Manifest </h4>
      </div>
      {!! Form::open(['route' => ['mailing.uploadmanifest',$id],'files' => true,'class'=>'uploadfile']) !!}
       
      <div class="modal-body" style="height: 130px">
          <div class="col-md-12 form-group filegroup">
              <label>Manifest Doc File:<br></label>

              {!!  Form::file('file','', ['class' => 'form-control']) !!}
          </div>
      </div>
      <div class="modal-footer">
            {!! Form::hidden('_method', 'POST') !!}
            {!! Form::hidden('redirect_to', URL::full()) !!}
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            <button class="btn btn-success" type="submit"><i calss="fa fa-times"></i> Upload Manifest</button>
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<a href="#" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-template-upload-{{ $id }}"><i class="fa fa-upload"></i> Upload Manifest </a>
