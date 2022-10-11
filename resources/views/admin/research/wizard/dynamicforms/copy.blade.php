<div class='row'>
    <div class="col-md-12 form-group">
        <label>Copy Recipient Type:</label>
        {!! Form::select('copy_recipient_type',['architect' => 'Architect', 'condo assoc' => 'Condo Assoc', 'Developer' => 'Developer', 'engineer' => 'Engineer', 'government agency' => 'Government Agency', 'homeowners assoc' => 'Homeowners Assoc', 'management co' => 'Management Co', 'surveying co' => 'Surveying Co', 'owner' => 'Owner', 'owner designated' => 'Owner Designated','other' => 'Other'], old("copy_recipient_type"), ['class' => 'form-control copy_recipient_type']) !!}
    </div>
</div>
<div class='row'>
    <div class="col-md-12 form-group other_type hidden">
        <label>Other:</label>
        {!!  Form::text('other_copy_recipient_type',old('other_copy_recipient_type'), ['class' => 'form-control other_copy_recipient_type','maxlength'=>'100']) !!}
    </div>
</div>