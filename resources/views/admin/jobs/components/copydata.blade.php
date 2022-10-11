<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>NOC Number:</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-1 form-group">
                <div class="checkbox checkbox-slider--b-flat">
                    <label>
                        <input name="copy_noc" type="checkbox"><span>&nbsp;</span>
                    </label>
                </div>
            </div>
            <div class="col-xs-11">
                {{ Form::text('xnoc_number',$xjob->noc_number,['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Job NOCs:</div>
    </div>
    <div class="panel-body">
        @include('admin.jobs.components.copynocs')
    </div>
</div>

<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Folio Number:</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-1 form-group">
                <div class="checkbox checkbox-slider--b-flat">
                    <label>
                        <input name="copy_folio" type="checkbox"><span>&nbsp;</span>
                    </label>
                </div>
            </div>
            <div class="col-xs-11">
                {{ Form::text('xfolio_number',$xjob->folio_number,['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

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
        @include('admin.jobs.components.copyparties')
    </div>
</div>
    
<div class="panel panel-default">
    <div class='panel-heading'>
        <div class='panel-title'>Job Attachments:</div>
    </div>
    <div class="panel-body">
        @include('admin.jobs.components.copyattachments')
    </div>
</div>


<button type="submit" class="btn btn-success btn-block"><i class="fa fa-copy"></i> Copy Selected</button>


<div>&nbsp;</div>
