<input type="hidden" id="parties_result_count" value="{{ count($parties) }}">
@foreach ($parties as $party)
@if($party->contact)
<div class="search-result">
    <div class="result-content">
        <h3><a href="{{route('parties.index',$party->job_id)}}">
            {{ $party->contact->full_name }}</a><br>
            <small>{{ title_case(str_replace("_", " ",$party->type))}}</small></h3>
    <a href="{{ route('jobs.edit',$party->job_id)}}" class="search-link"><i class="fa fa-pencil"></i> {{ $party->job->name}}</a>
    <p>
      {{ $party->contact->search_string }}
    </p>
    </div>
</div>
<div class="hr-line-dashed"></div>
@endif
@endforeach
