<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="thumbnail">
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
    <p>Source: ({{$jobparty->source ? $jobparty->source : 'Not Selected'}})</p>
    @if (is_null($jobparty->contact->email))
    @else
    <p><a href="mailto:{{ $jobparty->contact->email }}"> {!! $jobparty->contact->email !!}</a></p>
    @endif
    <p><i class="fa fa-envelope-o  fa-fw"></i> {!! $jobparty->contact->full_address !!}</p>

     @if (is_null($jobparty->contact->phone))
    @else
    <p><i class="fa fa-phone fa-fw"></i> {!! $jobparty->contact->phone !!}</a></p>
    @endif
    @if (is_null($jobparty->contact->mobile))
    @else
    <p><i class="fa fa-mobile fa-fw"></i> {!! $jobparty->contact->mobile !!}</a></p>
    @endif
    
    @if (is_null($jobparty->bond_type))
    @else
    <p><b>Bond type:</b> {{ title_case($jobparty->bond_type) }}</p>
    @endif
    
    
    @if (is_null($jobparty->bond_pdf))
    @else
    <p><a href="{{ route('parties.downloadbond',[$jobparty->job_id,$jobparty->id]) }}"><i class="fa fa-file"></i> {{$jobparty->bond_pdf_filename}}</a>({{ number_format($jobparty->bond_pdf_filename_size/1024,2)}}KB)</p>
    @endif
    
    @if (is_null($jobparty->bond_date))
    @else
    <p><b>Bond date:</b> {{ date('m/d/Y', strtotime($jobparty->bond_date ))}}</p>
    @endif
    
    @if (is_null($jobparty->bond_amount))
    @else
    <p><b>Bond amount:</b> {{  number_format($jobparty->bond_amount,2)}}</p>
    @endif
    
    @if (is_null($jobparty->bond_bookpage_number))
    @else
    <p><b>Bond Number:</b> {{ $jobparty->bond_bookpage_number}}</p>
    @endif
    
    @if (is_null($jobparty->landowner_deed_number))
    @else
    <p><b>Deed number:</b> {{ $jobparty->landowner_deed_number}}</p>
    @endif
    
    @if ($jobparty->type == "landowner")
    <p><b>Lien Prohibition:</b> {{ ($jobparty->landowner_lien_prohibition == 0) ? 'No' :'Yes' }}</p>
    @endif
    
    @if (is_null($jobparty->leaseholder_type))
    @else
    <p><b>Leaseholder Type: </b> {{ title_case($jobparty->leaseholder_type) }}</p>
    @endif
    
    @if (is_null($jobparty->leaseholder_lease_number))
    @else
    <p><b>Lease number:</b> {{ $jobparty->leaseholder_lease_number}}</p>
    @endif
    
    @if (is_null($jobparty->leaseholder_bookpage_number))
    @else
    <p><b>Lease book page number:</b> {{ $jobparty->leaseholder_bookpage_number}}</p>
    @endif
    
    @if (is_null($jobparty->leaseholder_lease_agreement))
    @else
    <p><b>Lease Agreement:</b>{{ Form::textarea('leaseholder_lease_agreement',$jobparty->leaseholder_lease_agreement,['class'=>'form-control','readonly'])}}</p>
    @endif
    
    @if (is_null($jobparty->copy_type))
    @else
    <p><b>Copy Recipient Type: </b> {{ title_case($jobparty->copy_type) }}</p>
    @endif
    
        <div class="controls">
            @component('admin.jobparties.components.deletemodal')
                @slot('id') 
                    {{ $jobparty->id }}
                @endslot
                @slot('job_party_name') 
                    {{ $jobparty->contact->full_name }}
                @endslot
                @slot('job_id') 
                    {{ $jobparty->job_id }}
                @endslot
                @slot('work_order') 
                 {{ $work_order }}
                @endslot
            @endcomponent 
            @if($work_order =='') 
            <a href="{{ route('parties.edit',[ $jobparty->job_id, $jobparty->id])}}" class="btn btn-success btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
            @else
            <a href="{{ route('parties.edit',[ $jobparty->job_id, $jobparty->id]). '?workorder='. $work_order}}" class="btn btn-success btn-xs" role="button" data-toggle="tooltip" data-placement="right" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
            @endif
           
             @component('admin.jobparties.components.copyparty')
                @slot('id') 
                    {{ $jobparty->id }}
                @endslot
                @slot('job_id') 
                    {{ $jobparty->job_id }}
                @endslot
                @slot('work_order') 
                 {{ $work_order }}
                @endslot
                @slot('parties_type', $parties_type) 
                @slot('xtype', $jobparty->type) 
                 
               
            @endcomponent
            
            
        </div>
   </div>
  </div>

