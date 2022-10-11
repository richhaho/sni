<div class='row'>
    <div class="col-md-12 form-group">
        <label>Lease Type:</label>
        {!! Form::select('leaseholder_type',['Lessee' =>'Lessee'], old("leaseholder_type"), ['class' => 'form-control lease-type']) !!}
    </div>
</div>
<div class='row'>
    <div class="col-md-6 form-group">
        <label>Lease Number:</label>
        <input name="leaseholder_lease_number"  value="{{ old("leaseholder_lease_number" )}}" class="form-control " data-toggle="tooltip" data-placement="top" title="">
    </div>
    <div class="col-md-6 form-group bookpage_number">
        <label>Book/Page Number:</label>
        {!!  Form::text('leaseholder_bookpage_number',old('leaseholder_bookpage_number'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class='row'>
    <div class="col-md-12 form-group">
        <label>Lease Agreement:</label>
        {!!  Form::textarea('leaseholder_lease_agreement',old('leaseholder_lease_agreement'), ['class' => 'form-control']) !!}
    </div>
</div>