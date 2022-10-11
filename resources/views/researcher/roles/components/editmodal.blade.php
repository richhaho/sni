<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-client-edit-{{ $role->id }}"><i class="fa fa-edit"></i> Edit</button>


<div class="modal fade" id="modal-client-edit-{{ $role->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Role</h4>
      </div>
      {!! Form::open (['route'=>['roles.update',$role->id],'method'=> 'PUT','autocomplete' => 'off']) !!}
      <div class="modal-body">
         <div class="row">
            <div class="col-xs-12 form-group">
                <label>Type:</label>
                {!!  Form::select('type',$types,$role->type,['class' => 'form-control']) !!}
            </div>
         </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <label>Name (no spaces):</label>
                {!!  Form::text('name',$role->name,['class' => 'form-control']) !!}
            </div>
         </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <label>Role Display Name:</label>
                {!!  Form::text('display_name',$role->display_name,['class' => 'form-control']) !!}
            </div>

         </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <label>Description:</label>
                {!!  Form::text('description',$role->description,['class' => 'form-control']) !!}
            </div>

         </div>
      </div>
      <div class="modal-footer">
           <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
            {!! Form::submit('Edit',['class'=>'btn btn-success pull-right']); !!}

      </div>
      {!! Form::close(); !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>