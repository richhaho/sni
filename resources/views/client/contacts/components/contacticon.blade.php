<div class="col-xs-12 col-sm-6 col-md-2">
    <div class="thumbnail  {{ ($contact->status == 0) ? 'associate-disabled': ''}}">
        <p class="h4"><i class="fa fa-user fa-fw"></i> {{ $contact->full_name}}
           
        </p>
        @if (is_null($contact->email))
        @else
        <p><a href="mailto:{{ $contact->email }}"> {!! $contact->email !!}</a></p>
        @endif


         @if (is_null($contact->phone))
        @else
        <p><i class="fa fa-phone fa-fw"></i> {!! $contact->phone !!}</a></p>
        @endif
        @if (is_null($contact->mobile))
        @else
        <p><i class="fa fa-mobile fa-fw"></i> {!! $contact->mobile !!}</a></p>
        @endif
        <p><i class="fa fa-envelope-o  fa-fw"></i> {!! $contact->full_address !!}</p>
        
           
        <div class=" controls">
           
        @if($contact->hot_id == 0 )   
            @if ($contact->primary==2)
                <a   class="btn btn-success btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Edit" disabled><i class="fa fa-pencil"></i> Edit</a>
            @else    
                <a href="{{ route('client.associates.edit',[$entity->id,$contact->id])}}" class="btn btn-success btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
            @endif
        @endif        
         @if($contact->status == 0 ) 
                <a href="{{ route('client.associates.enable',[$entity->id,$contact->id]) . '?page=1' }}" class="btn btn-warning btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Enable"><i class="fa fa-check"></i> Enable</a>
         @else
                <a href="{{ route('client.associates.disable',[$entity->id,$contact->id]) . '?page=1' }}" class="btn btn-warning btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Disable"><i class="fa fa-ban"></i> Disable</a>
         @endif
        </div>
    </div>
</div>
