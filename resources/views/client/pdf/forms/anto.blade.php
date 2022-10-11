@extends('client.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Date:</label>
            {!! Form::text('nto_date',$nto_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Notice #: </label>
            {!! Form::text('wo_number',$wo_number,['class'=>'form-control', 'readonly'=>'true'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Original Notice #: </label>
            {!! Form::text('wo_nto_number',$wo_nto_number,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12">
            <div class="form-group">
                <label>Property Owner Firm Name: </label>
                {!! Form::text('land_owner_firm_name',$land_owner_firm_name,['class'=>'form-control'])!!}
            </div>
            </div>
           
        </div>
        
        <div class="row">
             <div class="col-xs-6">
            <div class="form-group">
                <label>Property Owner Phone: </label>
                {!! Form::text('land_owner_phone',$land_owner_phone,['class'=>'form-control'])!!}
            </div>
                 </div><div class="col-xs-6">
           <div class="form-group">
                <label>Property Owner Email: </label>
                {!! Form::text('land_owner_email',$land_owner_email,['class'=>'form-control'])!!}
            </div>
                     </div>
        </div>
         <div class="row">
             <div class="col-xs-4">
             <div class="form-group">
                <label>Deed: </label>
                {!! Form::text('deed',$deed,['class'=>'form-control'])!!}
            </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <label>NOC #: </label>
                    {!! Form::text('noc',$noc,['class'=>'form-control'])!!}
                </div>
             </div>
             <div class="col-xs-4">
                <div class="form-group">
                    <label>Project #: </label>
                    {!! Form::text('project_number',$project_number,['class'=>'form-control'])!!}
                </div>
                     
             </div>
         </div>
        <div class="row">
            <div class="form-group">
                <label>Property Owner Address: </label>
                {!! Form::textarea('land_owner_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n", $land_owner_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <table style="width:100%">
            <tr class="leaseholders{{$page_id}}">
         @foreach($leaseholders as $key=>$ls) 
         <td data-id="{{$key}}">
                <div class="row">
                    <div class="col-xs-12">
                   <div class="form-group">
                       <label>
                           @if(count($leaseholders) > 1)
                           <a href="#" class="delete-leaseholder" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-times-circle"></i></span></a>
                           @endif
                           Lease Holder Name: </label>
                       {!! Form::text("leaseholders[" . $key ."][full_name]",$ls["full_name"],['class'=>'form-control'])!!}
                   </div>
                   </div>
                   
               </div>
             <div class="row">
                 <div class="col-xs-6">
                        <div class="form-group">
                       <label>Lease Holder Phone: </label>
                       {!! Form::text("leaseholders[" . $key ."][phone]",$ls["phone"],['class'=>'form-control'])!!}
                        </div>
                  </div>
                  <div class="col-xs-6">
                        <div class="form-group">
                       <label>Lease Holder Email: </label>
                       {!! Form::text("leaseholders[" . $key ."][email]",$ls["email"],['class'=>'form-control'])!!}
                        </div>
                    </div>
             </div>
               <div class="row">
                   <div class="form-group">
                       <label>Lease Holder Address: </label>
                       {!! Form::textarea("leaseholders[" . $key ."][address]",preg_replace('/\<br(\s*)?\/?\>/i', "\n",$ls["address"]),['class'=>'form-control','rows'=>5])!!}
                   </div>
               </div>
        
        </td>
           
            
        @endforeach
            <td class="add-cell{{$page_id}}" style="text-align: center; vertical-align: middle; padding: 10px"><a href="#" class="add-leaseholder" data-id="{{$page_id}}"><i class="fa fa-5x fa-plus-circle"></i><br>Add Leaseholder</a></td>
            </tr>
        </table>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
    </div>
     <div class="row">
        <div class="form-group">
            <label>Materials: </label>
            {!! Form::textarea('materials',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$materials),['class'=>'form-control','rows'=>5])!!}
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
                <label>Folio: </label>
                {!! Form::text('folio',$folio,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Legal Description: </label>
                {!! Form::textarea('legal_description',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$legal_description),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <table style='width:100%'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th></th>
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
    <div class="col-xs-6">
        
         <div class="row">
            <div class="form-group">
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Client Phone: </label>
                {!! Form::text('client_phone',$client_phone,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Client Fax: </label>
                {!! Form::text('client_fax',$client_fax,['class'=>'form-control'])!!}
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <label>Client Address: </label>
                {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
         <div class="row">
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
        </div>
        {!! Form::hidden('barcode_id',$barcode_id)!!}
        {!! Form::hidden('barcode_key',$barcode_key)!!}
    </div>
@overwrite