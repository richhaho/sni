
<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Legal Description:</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-1 form-group">
                <div class="checkbox checkbox-slider--b-flat">
                    <label>
                        <input name="copy_legal" type="checkbox"><span>&nbsp;</span>
                    </label>
                </div>
            </div>
            <div class="col-xs-11">
                {{ Form::textarea('xlegal_description',$xjob->legal_description,['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
    
<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Job Parties:</div>
    </div>
    <div class="panel-body">
        @include('researcher.jobs.components.copyparties')
    </div>
</div>
    
<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Job Attachments:</div>
    </div>
    <div class="panel-body">
        @include('researcher.jobs.components.copyattachments')
    </div>
</div>


<button type="submit" class="btn btn-success btn-block"><i class="fa fa-copy"></i> Copy Selected</button>


<div>&nbsp;</div>
