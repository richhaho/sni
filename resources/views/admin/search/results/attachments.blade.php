<input type="hidden" id="attachments_result_count" value="{{ count($attachments) }}">
@foreach ($attachments as $attachment)
<div class="search-result">
    <div class="result-content">
        <div class="row">
              @if (isset($attachment->attachable->id))
                @if ($attachment->attachable_type =="App\Job")
                <div class="col-xs-2">
                <img src="{{ route('jobs.showthumbnail',[$attachment->attachable->id,$attachment->id])}}"  class="img-responsive img-rounded">
                </div>
                <div class="col-xs-8">
                    <h3><a href="{{ route('jobs.edit',$attachment->attachable->id)}}#attachments">{{ $attachment->original_name }}</a></h3>
                    <a href="{{ route('jobs.edit',$attachment->attachable->id)}}#attachments" class="search-link"><i class="fa fa-pencil"></i> Manage Attachment on Job</a>
                    <p>
                      {{$attachment->description}}
                    </p>
                </div>
                @else
                <div class="col-xs-2">
                <img src="{{ route('workorders.showthumbnail',[$attachment->attachable->id,$attachment->id])}}"  class="img-responsive img-rounded">
                </div>
                <div class="col-xs-8">
                    <h3><a href="{{ route('workorders.edit',$attachment->attachable->id)}}#attachments">{{ $attachment->original_name }}</a></h3>
                    <a href="{{ route('workorders.edit',$attachment->attachable->id)}}#attachments" class="search-link"><i class="fa fa-pencil"></i> Manage Note on Work Order</a>
                    <p>
                      {{$attachment->description}}
                    </p>
                </div>
                @endif
              @endif
            
        </div>
        
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
