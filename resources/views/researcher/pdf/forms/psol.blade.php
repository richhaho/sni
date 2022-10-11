@extends('researcher.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Phone: </label>
            {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
        </div>
        </div>
          <div class="col-xs-6">
        <div class="form-group">
            <label>Client Gender: </label>
            {!! Form::select('client_heshe',['He'=>'Male','She'=>'Female'],$client_heshe,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Client Address: </label>
            {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>
  
    <div class="row">
                
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client County: </label>
            {!! Form::text('client_county',$client_county,['class'=>'form-control'])!!}
        </div>
        </div>
       
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Title </label>
            {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
        </div>
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
         <div class="row">
            <div class="form-group">
                <label>Job County: </label>
                {!! Form::text('job_county',$job_county,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="row">
            <div class="form-group">
                <label>Legal Description: </label>
                {!! Form::textarea('legal_description',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$legal_description),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="row ">
        
        <div class="col-xs-6">
        <div class="form-group">
            <label>Notice #: </label>
            {!! Form::text('nto_number',$nto_number,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Property Owner Name: </label>
                {!! Form::text('land_owner_name',$land_owner_name,['class'=>'form-control'])!!}
            </div>
        </div>

    </div>
    
    
        <div class="row">
            <div class="col-xs-6">
            <div class="form-group">
                <label>Partial Payment Amount: </label>
                {!! Form::number('pp_amount',$pp_amount,['class'=>'form-control', 'min' => '0', 'step'=>'0.01'])!!}
            </div>
            </div>
             <div class="col-xs-6">
            <div class="form-group">
                <label>Outstanding Balance: </label>
                {!! Form::number('pp_outstanding',$pp_outstanding,['class'=>'form-control', 'min' => '0', 'step'=>'0.01'])!!}
            </div>
            </div>
            
        </div>
    
    <div class="row ">
        
        <div class="col-xs-6">
            <div class="form-group">
                <label>Lien Date: </label>
                {!! Form::text('lien_date',$lien_date,['class'=>'form-control'])!!}
            </div>
        </div>
         <div class="col-xs-6">
            <div class="form-group">
                <label> Book Number & Page: </label>
                {!! Form::text('field_book_page_number',$field_book_page_number,['class'=>'form-control noucase'])!!}
            </div>
        </div>
        
    </div>
   <div class="row ">
        
        <div class="col-xs-6">
        <div class="form-group">
            <label>Month: </label>
            {!! Form::select('month',['january'=>'January','february'=>'February','march'=>'March','april'=>'April','may'=>'May','june'=>'June','july'=>'July','august'=>'August','september'=>'September','october'=>'October','novomber'=>'November','december'=>'December'],$month,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Year: </label>
                {!! Form::text('year',$year,['class'=>'form-control'])!!}
            </div>
        </div>

    </div>

    <div class="col-xs-12">
        
         <div class="row">
            <div class="form-group">
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>

        
         <div class="row">
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
@endsection