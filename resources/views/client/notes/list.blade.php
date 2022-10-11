<table class="table">
    <thead>
        <tr>
            <td>Note</td>
            <td>Entered</td>
            <td>Name</td>
           
            <td class="col-xs-2">Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($notes as $note)
        
        <tr>
            <td>{{ $note->note_text}}</td>
            <td>{{ date('m/d/Y H:i:s', strtotime($note->entered_at)) }}</td>
            <td>@if (isset($note->writer->full_name)){{ $note->writer->full_name }} @else Deleted User @endif</td>
          
            <td>
            @if (isset($note->writer->client_id))
            @if ($note->writer->client_id!=0)
             @component('client.notes.components.deletemodal')
                @slot('id') 
                    {{ $note->id }}
                @endslot
                @slot('e_name') 
                       {{ $e_name }}
                @endslot
                @slot('e_id') 
                    {{ $e_id }}
                @endslot
            @endcomponent
            &nbsp;<a href="{{ route('client.notes.edit',[$e_name , $e_id,$note->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i>Edit</a>
            @endif
            @endif
        </td>
        </tr>
        @endforeach
    </tbody>
</table>

