<div class="col-xs-1 form-group">
    <div class="checkbox checkbox-slider--b-flat">
        <label>
            <input name="copy_party[{{$jobparty->id}}]" type="checkbox"><span>&nbsp;</span>
        </label>
    </div>
</div>
<div class="col-xs-11">
    <div class="thumbnail">
    <p><i class="fa fa-user fa-fw"></i> {{ $jobparty->contact->full_name}}
        
    @if($jobparty->contact->entity->firm_name)
    <span> <small>{{ $jobparty->contact->entity->firm_name }}</small></span> 
    @endif
    </p>
    @if (is_null($jobparty->contact->email))
    @else
    <p><a href="mailto:{{ $jobparty->contact->email }}"> {!! $jobparty->contact->email !!}</a></p>
    @endif
    <p><i class="fa fa-envelope-o  fa-fw"></i> {!! $jobparty->contact->full_address !!}</p>
       
   </div>
</div>
