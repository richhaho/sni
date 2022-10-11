@extends('researcher.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Client Phone: </label>
            {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
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
            <label>Job Name: </label>
            {!! Form::text('job_name',$job_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Job County: </label>
            {!! Form::text('job_county',$job_county,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
      
        <div class="row">
            <div class="col-xs-6">
             <div class="form-group">
                <label>Job Address: </label>
                {!! Form::textarea('job_address',preg_replace("/\<br(\s*)?\/?\>/i", "\n",$job_address),['class'=>'form-control','rows'=>5])!!}
            </div>
            </div>
            <div class="col-xs-6">
                 <div class="form-group">
                    <label>Folio Number: </label>
                    {!! Form::text('folio',$folio,['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="col-xs-6">
                 <div class="form-group">
                    <label>Client County: </label>
                    {!! Form::text('client_county',$client_county,['class'=>'form-control'])!!}
                </div>
            </div>
        </div>
   
     <div class="row">
        <div class="col-xs-12">
       
            <div class="form-group">
                <label>Improvements: </label>
                {!! Form::textarea('improvements',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$improvements),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Property Owner Firm Name: </label>
            {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
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
        <div class="form-group">
            <label>Property Owner Address: </label>
            {!! Form::textarea('land_owner_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$land_owner_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>   
    
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Firm Name: </label>
            {!! Form::text('gc_firm_name',$gc_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Name: </label>
            {!! Form::text('gc_name',$gc_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>General Contractor Phone: </label>
            {!! Form::text('gc_phone',$gc_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>General Contractor Address: </label>
            {!! Form::textarea('gc_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$gc_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>  
    
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bond Firm Name: </label>
            {!! Form::text('bond_firm_name',$bond_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bond Name: </label>
            {!! Form::text('bond_name',$bond_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bond Phone: </label>
            {!! Form::text('bond_phone',$bond_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>Bond Address: </label>
            {!! Form::textarea('bond_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$bond_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>  
    
        <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bank Firm Name: </label>
            {!! Form::text('bank_firm_name',$bank_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bank Name: </label>
            {!! Form::text('bank_name',$bank_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Bank Phone: </label>
            {!! Form::text('bank_phone',$bank_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>Bank Address: </label>
            {!! Form::textarea('bank_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$bank_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div> 
    
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Firm Name: </label>
            {!! Form::text('copy_firm_name',$copy_firm_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Name: </label>
            {!! Form::text('copy_name',$copy_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-4">
        <div class="form-group">
            <label>Copy Phone: </label>
            {!! Form::text('copy_phone',$copy_phone,['class'=>'form-control'])!!}
        </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group">
            <label>Copy Address: </label>
            {!! Form::textarea('copy_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$copy_address),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div> 
    
   <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>Copy Firm Name: </label>
                {!! Form::text('copyl_firm_name',$copyl_firm_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
            <label>Copy Name: </label>
            {!! Form::text('copyl_name',$copyl_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
            <label>Copy Phone: </label>
            {!! Form::text('copyl_phone',$copyl_phone,['class'=>'form-control'])!!}
            </div>
        </div>
        
  
       <div class="col-xs-6">
        <div class="form-group">
            <label>Copy Address: </label>
            {!! Form::textarea('copyl_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$copyl_address),['class'=>'form-control','rows'=>5])!!}
        </div>
       </div>
    </div> 
    
@endsection