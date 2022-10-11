
<div class="col-xs-12 col-md-4">
    @if($xpayment == "")


    {!! Form::open(['route' => ['jobpayments.store',$job->id]]) !!}
    <div class="col-xs-12 form-group">
        <label>Paid Date: <span class="text-danger">*</span></label>
        {!!  Form::text('payed_on','', ['class' => 'form-control date-picker','id' =>'payed_on', 'data-date-format' => 'mm/dd/yyyy']) !!}
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
        <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Add Payment</button>
    </div>
    <div class="col-xs-12">
    <span class="text-danger">*</span> Mandatory fields
    </div>
    {!! Form::close() !!}
    @else
        {!! Form::open(['route' => ['jobpayments.update',$job->id,$xpayment->id], 'method'=> 'PUT', 'id'=> 'edit_payment_form' . $xpayment->id]) !!}
         <div class="col-xs-12 form-group">
             <label>Paid Date: <span class="text-danger">*</span></label>
            {!!  Form::text('payed_on',old("payed_on", (strlen($xpayment->payed_on) > 0) ? date('m/d/Y', strtotime($xpayment->payed_on)): ''), ['class' => 'form-control date-picker','id' =>'payed_on', 'data-date-format' => 'mm/dd/yyyy']) !!}
        </div>
        <div class="col-xs-12 form-group">
            <label>Amount: <span class="text-danger">*</span></label>
            {!!  Form::number('amount',old('amount',$xpayment->amount), ['class' => 'form-control','step' => '0.01', 'min' => '0']) !!}
        </div>
         <div class="col-xs-12 form-group">
            <label>Description:</label>
            {!!  Form::textarea('description',old('description',old('description',$xpayment->description)), ['class' => 'form-control']) !!}
        </div>
       <div class="col-xs-6 col-xs-offset-6 form-group ">
           <button class="btn btn-success pull-right" type="submit" form="edit_payment_form{{$xpayment->id}}"> <i class="fa fa-floppy-o"></i> Save</button>
       </div>
        <div class="col-xs-12">
        <span class="text-danger">*</span> Mandatory fields
        </div>
       {!! Form::close() !!}
    @endif
</div>



 <div class="col-md-8">
     @include('admin.jobs.payments.list', ['payments' => $payments])

 </div>


