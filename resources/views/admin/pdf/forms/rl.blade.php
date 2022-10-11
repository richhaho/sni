@extends('admin.pdf.forms.formbase')

@section('fields')
{!! Form::hidden('job_type',$job_type)!!}
{!! Form::hidden('barcode','')!!}
    <div class="row ">
            <div class="col-xs-6 ">
                    <div class="row">
                    <div class="form-group">
                        <label>Dated on: </label>
                        {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
                    </div>
                    </div>
            </div>
            <div class="col-xs-6 ">
                    <div class="row">
                    <div class="form-group">
                        <label>NTO Completed Date: </label>
                        {!! Form::text('nto_last_date',$nto_last_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
                    </div>
                    </div>
            </div>
         <div class="col-xs-6">
        <div class="form-group">
            <label>Unpaid Balance: </label>
            {!! Form::number('unpaid_balance',$unpaid_balance,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Notice Number: </label>
            {!! Form::text('nto_number',$nto_number,['class'=>'form-control '])!!}
        </div>
        </div>
        {!! Form::hidden('nto_date',$nto_date,['class'=>'form-control '])!!}
        <div class="col-xs-12">
        <div class="form-group">
            <label>Landowner Firm Name: </label>
            {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Landowner Address: </label>
                {!! Form::textarea('land_owner_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$land_owner_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
       
    </div>
<div class="row">
        <div class="form-group">
            <label>Text Content: </label>
            {!! Form::text('text_content',$text_content,['class'=>'form-control noucase'])!!}
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Customer (Order by) Name: </label>
            {!! Form::text('customer_name',$customer_name,['class'=>'form-control'])!!}
        </div>
    </div>
     <div class="col-xs-6">
        <div class="row">
            <div class="form-group">
                <label>Job Name: </label>
                {!! Form::text('job_name',$job_name,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Job Address: </label>
                {!! Form::textarea('job_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$job_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
         
    </div>
   
    <div class="col-xs-6 ">
        <table style='width:100%'>
            <thead>
                <tr>
                   
                    <th>Name</th>
                </tr>
            </thead>
            <tbody class="parties{{$page_id}}">
                @foreach($parties as $key => $pt)
                <tr data-id="{{$key}}">
                  
                    <td>{!! Form::text("parties[" . $key ."][company_name]",$pt["company_name"],['class'=>'form-control'])!!}</td>
                    <td style="padding:5px;"><a href="#" class="delete-line" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#" class="add-line-party" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Line</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        
         <div class="col-xs-4">
        <div class="form-group">
            <label>Client Firm Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
         <div class="col-xs-4">
        <div class="form-group">
            <label>Client Contact Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                <label>Client Phone: </label>
                {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="col-xs-12">
                <div class="form-group">
                    <label>Client Address: </label>
                    {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
                </div>
            </div>
    </div>
    


@overwrite