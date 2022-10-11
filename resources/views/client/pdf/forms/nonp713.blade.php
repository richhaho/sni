@extends('client.pdf.forms.formbase')

@section('fields')
<style type="text/css">
    #page{max-height: 30in !important;}
</style>
{!! Form::hidden('job_type',$job_type)!!}
{!! Form::hidden('barcode','')!!}
    <div class="col-xs-12 ">
        <div class="row">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="checkbox checkbox-slider--b-flat">
                    <label>
                        <input name="has_amended" type="checkbox" {{ $has_amended ? 'checked' :''}}><span>Amended?</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-6 ">
        <div class="row">
            <div class="form-group">
                <label>General Contractor Firm Name: </label>
                {!! Form::text('gc_firm_name',$gc_firm_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
                <label>General Contractor Address: </label>
                {!! Form::textarea('gc_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n", $gc_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
        <div class="row" style="display: none">
            <div class="form-group">
                <label>Bond Number: </label>
                {!! Form::text('bond_number',$bond_number,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>NOC: </label>
                {!! Form::text('noc',$noc,['class'=>'form-control'])!!}
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Sworn Date : </label>
                {!! Form::text('sworn_at',$sworn_at,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label>Dated on: </label>
                {!! Form::text('dated_on',$dated_on,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="row">
            <div class="form-group">
                <label>Client Name: </label>
                {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
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
        <div class="row">
            <div class="form-group">
                <label>Materials: </label>
                {!! Form::textarea('materials',preg_replace('/\<br(\s*)?\/?\>/i', " \n",$materials),['class'=>'form-control','rows'=>5])!!}
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
            <div class="form-group col-md-6">
                <label>Amount Due: </label>
                {!! Form::number('amount_due',$amount_due,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
            </div>             
            <div class="form-group col-md-6">
                <label>Amount Paid: </label>
                {!! Form::number('amount_paid',$amount_paid,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Retainage: </label>
                {!! Form::number('retainage',$retainage,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Interest Amount Due: </label>
                {!! Form::number('interest_amount_due',$interest_amount_due,['class'=>'form-control','min'=>0, 'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Last Day On Job: </label>
                {!! Form::text('last_day_on_job',$last_day_on_job,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Expected Amount Due: </label>
                {!! Form::text('expected_amount_due',$expected_amount_due,['class'=>'form-control'])!!}
            </div>
            <div class="form-group col-md-12">
                <label>Lienor expects to continue furnishing materials? </label>
                {!! Form::select('continue_furnishing',['Yes'=>'Yes','No'=>'No'],$continue_furnishing,['class'=>'form-control'])!!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label>Include Statement of Account Line Items? </label>
                {!! Form::select('include_soa_line_items',['Yes'=>'Yes','No'=>'No'],$include_soa_line_items,['class'=>'form-control'])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Total amount of the contract: </label>
                {!! Form::number('total_contract_amount',$total_contract_amount,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Total amount of Change Orders: </label>
                {!! Form::number('total_changeorder_amount',$total_changeorder_amount,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Total amount invoiced: </label>
                {!! Form::number('total_invoice_amount',$total_invoice_amount,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Total paid to date: </label>
                {!! Form::number('total_paid_date',$total_paid_date,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label>Notice Date: </label>
                {!! Form::text('notice_date',$notice_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
            </div>
            <div class="form-group col-md-6">
                <label>Certified Number: </label>
                {!! Form::text('certified_number',$certified_number,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    
    <div class="col-xs-12">
        <table style='width:100%'>
            <thead>
                <tr>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="parties{{$page_id}}">
                @foreach($parties as $key => $pt)
                <tr data-id="{{$key}}">
                    
                    <td>{!! Form::text("parties[" . $key ."][company_name]",$pt["company_name"],['class'=>'form-control'])!!}</td>
                    <td style="padding:5px;"><a class="delete-line" data-id="{{$key}}" style="cursor:pointer;"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right;"><a class="add-line-party" data-page-id="{{$page_id}}" style="cursor:pointer;"><i class="fa fa-plus-circle"></i> Add Line</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-xs-12">
        <div class="row">    
            <div class="form-group">
                <label>Does customer work directly for contractor?</label>
                {!! Form::select('customer_working_for_contract',['Yes'=>'Yes','No'=>'No'],$customer_working_for_contract,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <table style='width:100%'>
            <thead>
                <tr>
                    <th>Documents List</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="documents_list{{$page_id}}">
                @foreach($documents_list as $key => $doc)
                <tr data-id="{{$key}}">
                    
                    <td>{!! Form::text("documents_list[" . $key ."][document_name]",$doc["document_name"],['class'=>'form-control'])!!}</td>
                    <td style="padding:5px;"><a class="delete-line" data-id="{{$key}}" style="cursor:pointer;"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right;"><a class="add-line-document" data-page-id="{{$page_id}}" style="cursor:pointer;"><i class="fa fa-plus-circle"></i> Add Document</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-xs-12">
        <div class="row">    
            <div class="form-group">
                <label>Lienor has recorded a claim of lien</label>
                {!! Form::select('has_recorded_col',['Yes'=>'Yes','No'=>'No'],$has_recorded_col,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
   
@overwrite