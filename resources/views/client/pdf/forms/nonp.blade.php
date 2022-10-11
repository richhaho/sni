@extends('client.pdf.forms.formbase')

@section('fields')
{!! Form::hidden('job_type',$job_type)!!}
{!! Form::hidden('barcode','')!!}
    <div class="col-xs-6 ">
        <div class="row">
        <div class="form-group">
            <label>Dated on: </label>
            {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
        <div class="row">
        <div class="form-group">
            <label>Force Date: </label>
            {!! Form::text('force_date',$force_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
        
        <div class="row">
        <div class="form-group">
            <label>Unpaid Balance: </label>
            {!! Form::number('unpaid_balance',$unpaid_balance,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
        </div>
        </div>
         <div class="row">
            <div class="form-group">
                <label>General Contractor Firm Name: </label>
                {!! Form::text('gc_firm_name',$gc_firm_name,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>General Contractor Address: </label>
                {!! Form::textarea('gc_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n", $gc_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        
        <div class="row">
           <div class="form-group">
               <label>Client Firm Name: </label>
               {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
           </div>
       </div>
        {!! Form::hidden('client_gender',$client_gender,['class'=>'form-control'])!!}
        <div class="row">
            <div class="form-group">
                <label>Client Address: </label>
                {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
        <div class="row">
        <div class="form-group">
            <label>Client Contact Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
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
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
            </div>
        </div>
         
    </div>

    <div class="col-xs-12">
     <div class="row">
        <div class="form-group">
            <label>Materials: </label>
            {!! Form::textarea('materials',preg_replace('/\<br(\s*)?\/?\>/i', " \n",$materials),['class'=>'form-control','rows'=>5])!!}
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Customer (Order by) Name: </label>
            {!! Form::text('customer_name',$customer_name,['class'=>'form-control'])!!}
        </div>
    </div>
    </div>
     <div class="col-xs-12">
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
                <label>Additional Text: </label>
                {!! Form::textarea('additional_text',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$additional_text),['class'=>'form-control noucase','rows'=>3])!!}
            </div>
        </div>
         <div class="row">
            <div class="form-group">
                <label class="hidden">Bond Number: </label>
                {!! Form::text('bond_number',$bond_number,['class'=>'form-control hidden'])!!}
            </div>
        </div>
    </div>
    <div class="clear-fix"></div>
     <div class="col-xs-12">
        <table style='width:100%'>

            <tbody class="sureties{{$page_id}}" id="sureties{{$page_id}}">
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#sureties{{$page_id}}" class="add-line-surety" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Surety</a></td>
                </tr>
                @foreach($sureties as $key => $st)
                <tr data-id="{{$key}}" >
                    <td>
                        <table style="width:100%"> 
                            <tr>
                                <td><label>Name</label></td>
                                <td>{!! Form::text("sureties[" . $key ."][name]",$st["name"],['class'=>'form-control'])!!}</td>
                                <td style="padding:5px;"><a href="#" class="delete-line-surety" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                            </tr>
                            <tr>
                                <td><label>Address</label></td>
                                <td >
                                    <textarea name="sureties[{{$key}}][address]" class="form-control">{{ preg_replace('/\<br(\s*)?\/?\>/i', "\n",$st["address"])}}</textarea>
                                </td>
                                <td></td>
                            <tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
    <div class="col-xs-12">
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
   
@overwrite