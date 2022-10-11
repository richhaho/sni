<table class="table">
    <thead>
        <tr>
            <td>Note</td>
            <td>Entered</td>
            <td>Name</td>
            <td>Viewable by customer</td>
            <td class="col-xs-2">Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($notes as $note)
        <tr>
            <td>{{ $note->note_text}}</td>
            <td>{{ date('m/d/Y H:i:s', strtotime($note->entered_at)) }}</td>
            <td>@if (isset($note->writer->full_name)){{ $note->writer->full_name }} @else Deleted User @endif</td>
            <td>{{ $note->viewable == 1 ? 'Yes' : 'No'}}</td>
            <td>
            @if(!Auth::user()->restricted || (Auth::user()->restricted && Auth::user()->id == $note->entered_by))
             @component('admin.notes.components.deletemodal')
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
            &nbsp;<a href="{{ route('notes.edit',[$e_name , $e_id,$note->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i>Edit</a></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

