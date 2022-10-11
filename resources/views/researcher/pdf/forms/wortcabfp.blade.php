@extends('researcher.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Dated On: </label>
            {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Amount: </label>
            {!! Form::number('amount',$amount,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
        </div>
        </div>
        
        <div class="col-xs-6">
         <div class="form-group">
            <label>Customer (Order by) Name: </label>
            {!! Form::text('customer_name',$customer_name,['class'=>'form-control'])!!}
        </div>
        </div>
        
        <div class="col-xs-6">
         <div class="form-group">
            <label>Property Owner: </label>
            {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    </div>

    

   
     <div class="col-xs-6">
         <div class="row">
            <div class="form-group">
                <label>Job Number: </label>
                {!! Form::text('nto_number',$nto_number,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Name: </label>
                {!! Form::text('job_name',$job_name,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Address: </label>
                {!! Form::textarea('job_address',preg_replace('/\<br(\s*)?\/?\>/i', "",$job_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
         <div class="row">
            <div class="form-group">
                <label>Job County: </label>
                {!! Form::text('job_county',$job_county,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
         <div class="col-xs-12">
            <div class="form-group">
                <label>Client Name: </label>
                {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
            </div>
        </div>
        
        <div class="col-xs-12">
            <div class="form-group">
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
            </div>
        </div>

         <div class="col-xs-12">
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
        </div>

    </div>
@overwrite