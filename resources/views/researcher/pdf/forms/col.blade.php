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
        <div class="col-xs-3">
        <div class="form-group">
            <label>Client County: </label>
            {!! Form::text('client_county',$client_county,['class'=>'form-control'])!!}
        </div>
        </div>
         <div class="col-xs-3">
        <div class="form-group">
            <label>Client Gender: </label>
            {!! Form::select('client_heshe',['He'=>'Male','She'=>'Female'],$client_heshe,['class'=>'form-control'])!!}
        </div>
        </div>
        <div class="col-xs-3">
        <div class="form-group">
            <label>Client Title </label>
            {!! Form::text('client_title',$client_title,['class'=>'form-control'])!!}
        </div>
        </div>
         <div class="col-xs-3">
        <div class="form-group">
            <label>Customer Name </label>
            {!! Form::text('customer_name',$customer_name,['class'=>'form-control'])!!}
        </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xs-12">
        <div class="form-group">
            <label>Job Materials: </label>
            {!! Form::textarea('materials',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$materials),['class'=>'form-control','rows'=>5])!!}
        </div>
        </div>
    </div>
      <div class="col-xs-6">
          <div class="row">
          <div class="form-group">
            <label>Job Starting Date: </label>
            {!! Form::text('job_start_date',$job_start_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
          </div>
          </div>
           <div class="row">
          <div class="form-group">
            <label>Last day on Job: </label>
            {!! Form::text('job_last_date',$job_last_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy'])!!}
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
        <div class="row">
            <div class="form-group">
                <label>Deed: </label>
                {!! Form::text('deed',$deed,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
                <label>Folio: </label>
                {!! Form::text('folio',$folio,['class'=>'form-control'])!!}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="row">
            <div class="form-group">
                <label>Total Value: </label>
                {!! Form::text('job_contract_amount',$job_contract_amount,['class'=>'form-control'])!!}
            </div>
            <div class="form-group">
                <label>Unpaid Balance: </label>
                {!! Form::text('unpaid_balance',$unpaid_balance,['class'=>'form-control'])!!}
            </div>
           
        </div>
        <div class="row">
            <div class="form-group">
                <label>Legal Description: </label>
                {!! Form::textarea('legal_description',preg_replace('/\<br(\s*)?\/?\>/i', "\n",$legal_description),['class'=>'form-control','rows'=>5])!!}
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-xs-4">
        <div class="form-group">
            <label>Date Notified: </label>
            {!! Form::text('nto_date',$nto_date,['class'=>'form-control date-picker', 'data-date-format' => 'mm/dd/yyyy' ])!!}
        </div>
        </div>
        
        <div class="col-xs-4">
        <div class="form-group">
            <label>Job #: </label>
            {!! Form::text('nto_number',$nto_number,['class'=>'form-control'])!!}
        </div>
        </div>
         <div class="col-xs-4">
        <div class="form-group">
            <label>NOC #: </label>
            {!! Form::text('noc',$noc,['class'=>'form-control'])!!}
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
    <div class="col-xs-6">
        <div class="row">
            <div class="form-group">
                <label>Property Owner Name: </label>
                {!! Form::text('land_owner_name',$land_owner_name,['class'=>'form-control'])!!}
            </div>
        </div>

    </div>
    <div class="col-xs-6">
            <div class="row">
            <div class="form-group">
                <label>
                    Lease Holder Name: </label>
                {!! Form::text("leaseholders[0][full_name]",$leaseholders[0]["full_name"],['class'=>'form-control'])!!}
            </div>

    </div>
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
  
@endsection