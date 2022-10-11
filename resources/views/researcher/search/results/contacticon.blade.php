<div class="col-xs-12 col-sm-6 col-md-2">
    <div class="thumbnail  {{ ($xentity->status == 0) ? 'associate-disabled': ''}}">
        <p class="h4"><i class="fa fa-user fa-fw"></i> {{ $xentity->full_name}}
    @if($xentity->entity->firm_name)
    <br><span> <small>{{ $xentity->entity->firm_name }}</small></span> 
    @endif
    </p>
    <p><i class="fa fa-envelope-o  fa-fw"></i> {!! $xentity->full_address !!}</p>
        
  
    </div>
  </div>
