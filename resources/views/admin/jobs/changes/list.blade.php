<table class="table">
    <thead>
        <tr>
            <td>Number</td>
            <td>Added on</td>
            <td>Amount</td>
            <td>Description</td>
            <td>Attachment</td>
            <td class="col-xs-2">Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($changes as $change)
        <tr>
            <td>{{ $change->number }}</td>
            <td>{{ (strlen($change->added_on) > 0) ? date('m/d/Y', strtotime($change->added_on)): 'N/A' }}</td>
            <td>{{ number_format($change->amount,2) }}</td>
            <td>{{ $change->description }}</td>
            <td> <a href="{{ route('jobchanges.showattachment',[$change->id])}}">{{$change->attached_file}}</a></td>
            <td>
             @component('admin.jobs.changes.components.deletemodal')
                @slot('id') 
                    {{ $change->id }}
                @endslot
                 @slot('job_id') 
                    {{ $job->id }}
                @endslot
            @endcomponent
            &nbsp;<a href="{{ route('jobchanges.edit',[$job->id ,$change->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i>Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

