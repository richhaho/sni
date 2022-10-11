<input type="hidden" id="notes_result_count" value="{{ count($notes) }}">
@foreach ($notes as $note)
<div class="search-result">
    <div class="result-content">
       @if ($note->noteable_type =="App\Job")
            <h3><a href="{{ route('jobs.edit',$note->noteable->id)}}#notes">{{ $note->noteable->name }}</a></h3>
            <a href="{{ route('jobs.edit',$note->noteable->id)}}#notes" class="search-link"><i class="fa fa-pencil"></i> Edit Note on Job</a>
            <p>
              {{$note->note_text}}
            </p>
        @else
            <h3><a href="{{ route('workorders.edit',$note->noteable->id)}}#notes">{{ $note->noteable->type }}</a></h3>
            <a href="{{ route('workorders.edit',$note->noteable->id)}}#notes" class="search-link"><i class="fa fa-pencil"></i> Edit Note on Work Order</a>
            <p>
              {{$note->note_text}}
            </p>
        @endif
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
