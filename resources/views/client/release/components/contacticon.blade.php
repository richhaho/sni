
        
    <p class="h4">
    @if($jobparty->contact->entity->firm_name)
     {{ $jobparty->contact->entity->firm_name }}<br />
        @if($jobparty->contact->full_name)
        <span> <small><i class="fa fa-user fa-fw"></i> {{ $jobparty->contact->full_name }}</small></span>
        @endif
    @else
    <i class="fa fa-user fa-fw"></i> {{ $jobparty->contact->full_name }}
    @endif
    </p>
   
    @if (is_null($jobparty->bond_type))
    @else
    <p><b>Bond type:</b> {{ title_case($jobparty->bond_type) }}</p>
    @endif

    @if (is_null($jobparty->leaseholder_type))
    @else
    <p><b>Leaseholder Type: </b> {{ title_case($jobparty->leaseholder_type) }}</p>
    @endif
    
   
    @if (is_null($jobparty->copy_type))
    @else
    <p><b>Copy Recipient Type: </b> {{ title_case($jobparty->copy_type) }}</p>
    @endif

  

