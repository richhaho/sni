<input type="hidden" id="client_result_count" value="{{ count($clients) }}">
@foreach ($clients as $client)
<div class="search-result">
    <div class="result-content">
    <h3><a href="{{ route('clients.edit',$client->id)}}">{{ $client->company_name }}</a></h3>
    <a href="{{ route('clients.edit',$client->id)}}" class="search-link"><i class="fa fa-pencil"></i> Edit Client</a>
    <p>
      {{$client->search_string}}
    </p>
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
