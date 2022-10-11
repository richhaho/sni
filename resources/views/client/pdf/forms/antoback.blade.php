@extends('client.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        
        <div class="col-xs-12">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
       
    </div>

    <div class="row ">
        
        <div class="col-xs-12">
        <div class="form-group">
            <label>Title of Client Signature: </label>
            {!! Form::text('client_signature',$client_signature,['class'=>'form-control'])!!}
        </div>
        </div>
       
    </div>

    <div class="row ">
        
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Fax: </label>
            {!! Form::text('client_fax',$client_fax,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Phone: </label>
            {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
        </div>
        </div>
       
    </div>
 
    <div class="row ">
        
        <div class="col-xs-12">
        <div class="form-group">
            <label>Client email: </label>
            {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
        </div>
        </div>
       
    </div>
    <div class="row ">
        
        <div class="col-xs-12">
        <div class="form-group">
            <label>Client Contact Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
       
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label>Client Address: </label>
                {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
@overwrite