
<div class="col-xs-12 col-md-4">
    @if($xchange == "")


    {!! Form::open(['route' => ['client.jobchanges.store',$job->id]]) !!}
    <div class="col-xs-12 form-group">
        <label>Number: <span class="text-danger">*</span></label>
        {!!  Form::text('number','', ['class' => 'form-control','maxlength'=>'50']) !!}
    </div>
    <div class="col-xs-12 form-group">
        <label>Added at: <span class="text-danger">*</span></label>
        {!!  Form::text('added_on','', ['class' => 'form-control date-picker','id' =>'added_on', 'data-date-autoclose' => 'true', 'data-date-format' => 'mm/dd/yyyy'  ]) !!}
    </div>
    <div class="col-xs-12 form-group">
        <label>Amount: <span class="text-danger">*</span></label>
        {!!  Form::number('amount','', ['class' => 'form-control','step' => '0.01', 'min' => '0']) !!}
    </div>
     <div class="col-xs-12 form-group">
        <label>Description:</label>
        {!!  Form::textarea('description','', ['class' => 'form-control']) !!}
    </div>
     <div class="col-xs-6 col-xs-offset-6 form-group ">
        <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Create Change</button>
    </div>
      <div class="col-xs-12">
        <span class="text-danger">*</span> Mandatory fields
        </div>
    {!! Form::close() !!}
    @else
        {!! Form::open(['route' => ['client.jobchanges.update',$job->id,$xchange->id], 'method'=> 'PUT', 'id'=> 'edit_change_form' . $xchange->id]) !!}
        <div class="col-xs-12 form-group">
            <label>Number: <span class="text-danger">*</span></label>
            {!!  Form::text('number',old('number',$xchange->number), ['class' => 'form-control']) !!}
        </div>
         <div class="col-xs-12 form-group">
             <label>Added at: <span class="text-danger">*</span></label>
            {!!  Form::text('added_on',old("added_on", (strlen($xchange->added_on) > 0) ? date('m/d/Y', strtotime($xchange->added_on)): ''), ['class' => 'form-control date-picker','id' =>'added_on', 'data-date-format' => 'mm/dd/yyyy']) !!}
        </div>
        <div class="col-xs-12 form-group">
            <label>Amount: <span class="text-danger">*</span></label>
            {!!  Form::number('amount',old('amount',$xchange->amount), ['class' => 'form-control','step' => '0.01', 'min' => '0']) !!}
        </div>
         <div class="col-xs-12 form-group">
            <label>Description:</label>
            {!!  Form::textarea('description',old('description',old('description',$xchange->description)), ['class' => 'form-control']) !!}
        </div>
       <div class="col-xs-6 col-xs-offset-6 form-group ">
           <button class="btn btn-success pull-right" type="submit" form="edit_change_form{{$xchange->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
       </div>
          <div class="col-xs-12">
        <span class="text-danger">*</span> Mandatory fields
        </div>
       {!! Form::close() !!}
    @endif
</div>



 <div class="col-md-8">
     @include('client.jobs.changes.list', ['changes' => $changes])

 </div>


