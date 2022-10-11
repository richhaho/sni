<div class="col-xs-12">
@foreach ($xjob->attachments as $attach)
    <div class="row">
        <div class="col-xs-1 form-group">
            <div class="checkbox checkbox-slider--b-flat">
                <label>
                    <input name="copy_attachment[{{$attach->id}}]" type="checkbox"><span>&nbsp;</span>
                </label>
            </div>
        </div>
        <div class="col-md-11 text-center">
             <div class="thumbnail">
                <img class="img-responsive" src="{{ route('jobs.showthumbnail',[$xjob->id,$attach->id])}}" alt="{{ $attach->type }}">
                <div class="caption">
                  <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                  <p>{{ $attach->description }}</p>
                </div>
              </div>
        </div>
    </div>
@endforeach
</div>