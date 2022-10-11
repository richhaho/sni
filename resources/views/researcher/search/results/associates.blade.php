<input type="hidden" id="associates_result_count" value="{{ count($associates) }}">
@foreach ($associates as $associate)
<div class="search-result">
    <div class="result-content">
    @if($associate->is_hot  )
    <h3><a href="{{ route('hotassociates.edit',[$associate->entity_id,$associate->id])}}"><i class="fa fa-fire"></i> {{ $associate->full_name }}</a></h3>
        <a href="{{ route('hotcontacts.edit',[$associate->entity->client_id,$associate->entity->id])}}" class="search-link"><i class="fa fa-building"></i> {{$associate->entity->firm_name}}</a><br>
        <a href="{{ route('hotassociates.edit',[$associate->entity_id,$associate->id])}}" class="search-link"><i class="fa fa-pencil"></i> Edit Hot Associate</a>
    @else
        <h3><a href="{{ route('associates.edit',[$associate->entity_id,$associate->id])}}">{{ $associate->full_name }}</a></h3>
        <a href="{{ route('contacts.edit',[$associate->entity->client_id,$associate->entity->id])}}" class="search-link"><i class="fa fa-building"></i> {{$associate->entity->firm_name}}</a><br>
        <a href="{{ route('associates.edit',[$associate->entity_id,$associate->id])}}" class="search-link"><i class="fa fa-pencil"></i> Edit Associate</a>
    @endif
    <p>
      {{$associate->search_string}}
    </p>
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
