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
            <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($note->entered_at))->diffForHumans() }}</td>
            <td>{{ $note->writer->full_name }}</td>
            <td>{{ $note->viewable == 1 ? 'Yes' : 'No'}}</td>
            <td>
             @component('researcher.notes.components.deletemodal')
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
        </tr>
        @endforeach
    </tbody>
</table>

