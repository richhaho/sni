<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    @if($jobparty->contact)
    <div class="thumbnail">
    <p class="h4"><i class="fa fa-user fa-fw"></i> {{ $jobparty->contact->full_name_or_entity}}
        
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
    @endif
</div>
