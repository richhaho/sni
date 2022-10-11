<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-item-edit-{{ $item->id }}"><i class="fa fa-edit"></i> Edit</button>


<div class="modal fade" id="modal-item-edit-{{ $item->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Item</h4>
      </div>
      {!! Form::open (['route'=>['pricelist.update',$item->id],'method'=> 'PUT','autocomplete' => 'off']) !!}
         <div class="modal-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    <label>Name:</label>
                    {!!  Form::text('description',$item->description,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <label>Price:</label>
                    {!!  Form::number('price',$item->price,['class' => 'form-control','min'=>'0','step'=>'0.01']) !!}
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