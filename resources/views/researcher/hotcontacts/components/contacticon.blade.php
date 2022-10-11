<div class="col-xs-12 col-sm-6 col-md-2">
    <div class="thumbnail {{ ($contact->status == 0) ? 'hotassociate-disabled': ''}}">
        @if(strlen($contact->full_name))
        <p class="h4"><i class="fa fa-user fa-fw"></i> {{ $contact->full_name}}</p>
        @else
         <p class="h4">&nbsp;</p>
        @endif         
    <p><i class="fa fa-envelope-o  fa-fw"></i> {!! $contact->full_address !!}</p>
    
        <div class=" controls">
           
        
        <a href="{{ route('hotassociates.edit',[$entity->id,$contact->id])}}" class="btn btn-success btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
         @if($contact->status == 0 ) 
         <a href="{{ route('hotassociates.enable',[$entity->id,$contact->id]) . '?page=' .$entities->currentPage() }}" class="btn btn-warning btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Enable"><i class="fa fa-check"></i> Enable</a>
         @else
         <a href="{{ route('hotassociates.disable',[$entity->id,$contact->id]) . '?page=' .$entities->currentPage()}}" class="btn btn-warning btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Disable"><i class="fa fa-ban"></i> Disable</a>
         @endif
        </div>
    </div>
  </div>
