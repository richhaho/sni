@extends('researcher.pdf.forms.formbase') 
@section('fields')
<div class="row ">
    <div class="col-xs-4">
        <div class="form-group">
            <label>Client Name: </label> {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Client Company Name: </label> {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Client Phone: </label> {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group">
        <label>Client Address: </label>
        {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n", $client_address),['class'=>'form-control','rows'=>5])!!}
    </div>
</div>
<div class="row ">
    <div class="col-xs-4">
        <div class="form-group">
            <label>Lien Date: </label> {!! Form::text('lien_date',$lien_date,['class'=>'form-control date-picker'])!!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Dated On: </label> {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format'
            => 'mm/dd/yyyy'])!!}
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Job County: </label> {!! Form::text('job_county',$job_county,['class'=>'form-control '])!!}
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label>Official Records & Book: </label> {!! Form::text('official_record_book',$official_record_book,['class'=>'form-control'])!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <label>Lienor Name: </label> {!! Form::text('lienor_company_name',$lienor_company_name,['class'=>'form-control'])!!}
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label>Lienor Address: </label> 
            {!! Form::textarea('lienor_address',preg_replace('/\<br(\s*)?\/?\>/i', "",$lienor_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>

</div>
<div class="col-xs-12">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label>Property Owner Firm Name: </label> {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
   <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>Property Owner Phone: </label> {!! Form::text('land_owner_phone',$land_owner_phone,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Property Owner Email: </label> {!! Form::text('land_owner_email',$land_owner_email,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Property Owner Address: </label> 
            {!! Form::textarea('land_owner_address',preg_replace("/\<br(\s*)?\/?\>/i", "\n", $land_owner_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>
</div>

@overwrite