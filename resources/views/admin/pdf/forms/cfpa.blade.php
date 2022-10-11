@extends('admin.pdf.forms.formbase')

@section('fields')
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Signed at: </label>
            {!! Form::text('signed_at',$signed_at,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
        </div>
        </div>
    
        <div class="col-xs-4">
        <div class="form-group">
            <label>Unpaid Balance: </label>
            {!! Form::number('unpaid_balance',$unpaid_balance,['class'=>'form-control ','min' => 0 ,'step' => 0.01])!!}
        </div>
        </div>
         <div class="col-xs-4">
         <div class="form-group">
            <label>Property Owner: </label>
            {!! Form::text('land_owner_firm_name',(strlen($land_owner_firm_name)) ? $land_owner_firm_name : $xleaseholders[0]['full_name'] ,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Company Name: </label>
            {!! Form::text('client_company_name',$client_company_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
        <div class="form-group">
            <label>Client Name: </label>
            {!! Form::text('client_name',$client_name,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Client Address: </label>
                {!! Form::textarea('client_address',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$client_address),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Client Email: </label>
                {!! Form::text('client_email',$client_email,['class'=>'form-control'])!!}
            </div>
        </div>
  
         <div class="col-xs-6">
        <div class="form-group">
            <label>Client Gender: </label>
            {!! Form::select('client_heshe',['He'=>'Male','She'=>'Female'],$client_heshe,['class'=>'form-control'])!!}
        </div>
        </div>
        
        
         <div class="col-xs-6">
            <div class="form-group">
                <label>Client County: </label>
                {!! Form::text('client_county',$client_county,['class'=>'form-control'])!!}
            </div>
        </div>
         <div class="col-xs-6">
            <div class="form-group">
                <label>Client Title: </label>
                {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
            </div>
        </div>

    </div>
<div class="row">
     <div class="col-xs-12">
        <table style='width:100%'>
            <thead>
                <tr>
                    <th>Lienor</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody class="lienors{{$page_id}}">
                @foreach($lienors as $key => $pt)
                <tr data-id="{{$key}}">
                    <td>{!! Form::text("lienors[" . $key ."][name]",$pt["name"],['class'=>'form-control'])!!}</td>
                    <td>{!! Form::text("lienors[" . $key ."][amount]",$pt["amount"],['class'=>'form-control'])!!}</td>
                    <td style="padding:5px;"><a href="#" class="delete-line" data-id="{{$key}}"><span class="text-danger"><i class="fa fa-minus-circle"></i></span></a></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right"><a href="#" class="add-line-lienor" data-page-id="{{$page_id}}"><i class="fa fa-plus-circle"></i> Add Line</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@overwrite