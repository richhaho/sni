<div class='row'>
    <div class="col-md-12 form-group">
        <label>Bond Type:</label>
        {!! Form::select('bond_type',['agent' => 'Agent', 'company' => 'Company'], '',['class' => 'form-control'])!!}
    </div>
</div>
<div class='row'>
    <div class="col-xs-12 form-group">
        <label>Bond Contract (PDF):</label>
        {!!  Form::file('bond_pdf','', ['class' => 'form-control']) !!}
    </div>
</div>
<div class='row'>
    <div class="col-md-4 form-group">
        <label>Bond date:</label>
        <input name="bond_date"  value="{{ old("bond_date" )}}" class="form-control date-picker" data-date-format="mm/dd/yy" data-date-autoclose="true" data-toggle="tooltip" data-placement="top" title="">
    </div>
    <div class="col-md-4 form-group">
        <label>Bond Number:</label>
        {!!  Form::text('bond_bookpage_number',old('bond_bookpage_number'), ['class' => 'form-control','maxlength'=>'50']) !!}
    </div>

    <div class="col-md-4 form-group">
        <label>Bond Amount:</label>
        {!!  Form::number('bond_amount',old('bond_amount'), ['class' => 'form-control', 'min'=>'0', 'step' => '0.01']) !!}
    </div>
</div>