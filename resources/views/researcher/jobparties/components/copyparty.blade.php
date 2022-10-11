
<button type="button" class="btn btn-primary btn-xs t" data-toggle="modal" data-target="#modal-copy-party-{{ $id }}"><i class="fa fa-copy"></i> Copy</button>


<div class="modal fade" id="modal-copy-party-{{ $id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
         <div class="modal-header text-left">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Copy Job Party</h4>
            </div>
         {!! Form::open(['url' => route('parties.copy',[$job_id,$id])]) !!}
        
            <div class="modal-body text-left">
                <p>Copy this job party as: {{ Form::select('party_type',array_diff_key($parties_type,[$xtype => 'delete']),'',['class' => 'form-control']) }}</p>
            </div>
            <div class="modal-footer">



              {{ Form::hidden('workorder', $work_order) }}
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;&nbsp;
                  <button class="btn btn-success" type="submit"><i calss="fa fa-times"></i> Copy</button>


            </div>
         {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>