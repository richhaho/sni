<input type="hidden" id="jobs_result_count" value="{{ count($jobs) }}">
@foreach ($jobs as $job)
<div class="search-result">
    <div class="result-content">
    <h3><a href="{{ route('jobs.edit',$job->id)}}">{{ $job->name }}</a></h3>
    <a href="{{ route('clients.edit',$job->client_id)}}" class="search-link"><i class="fa fa-users"></i> {{ $job->client->company_name}}</a><br>
    <a href="{{ route('jobs.edit',$job->id)}}" class="search-link"><i class="fa fa-link"></i> Edit Job</a>
    <p>
      {{$job->search_string}}
    </p>
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
