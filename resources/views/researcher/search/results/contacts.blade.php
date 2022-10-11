<input type="hidden" id="contacts_result_count" value="{{ count($contacts) }}">
@foreach ($contacts as $contact)
<div class="search-result">
    <div class="result-content">
     @if($contact->client_id ==0)
     <h3><a href="{{ route('hotcontacts.edit',[$contact->id])}}"><i class="fa fa-fire"></i> {{ $contact->firm_name }}</a></h3>
    @else
    <h3><a href="{{ route('contacts.edit',[$contact->client_id,$contact->id])}}">{{ $contact->firm_name }}</a></h3>
    <i class="fa fa-building"></i> Client name: <strong> {{$contact->client->company_name }}</strong><br>
    @endif
    <a href="{{ route('contacts.edit',[$contact->client_id,$contact->id])}}" class="search-link"><i class="fa fa-pencil"></i> Edit Contact</a> &nbsp;
    
    <div class="row same-height" >
        @foreach($contact->contacts()->orderBy('primary','desc')->get() as $xentity) 

            @include('researcher.search.results.contacticon')


        @endforeach

        <div class="col-xs-12 col-sm-6 col-md-2" >
            <div class="thumbnail text-center"  >
                 @if($contact->client_id ==0)
                <a href="{{ route('hotassociates.create',$contact->id) }}">
                   @else 
                   <a href="{{ route('associates.create',$contact->id) }}">
                   @endif
                <i class="fa fa-user-plus fa-5x thumbnail-icon"></i>
                <h4>New Associate</h4>
                </a>
            </div>
        </div>

    </div>
    </div>
</div>
<div class="hr-line-dashed"></div>
@endforeach
