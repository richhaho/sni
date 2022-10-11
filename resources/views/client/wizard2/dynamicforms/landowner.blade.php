<div class='row'>
    <div class="col-md-12 form-group hidden">
        <label>Deed Number:</label>
        {!!  Form::text('landowner_deed_number',old('landowner_deed_number'), ['class' => 'form-control', 'maxlength'=>'80']) !!}
    </div>
</div>
<div class='row'>
    <div class="col-md-12 form-group">

    <div class="checkbox checkbox-slider--b-flat">
        <label>
        <input name="lien_prohibition" type="checkbox"><span>Lien Prohibition</span>
        </label>
    </div>
 </div> 
   
</div>