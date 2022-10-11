<table class="table">
    <thead>
        <tr>
            <td>Paid on</td>
            <td>Amount</td>
            <td>Description</td>
            <td>Attachment</td>
            <td class="col-xs-2">Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $pay)
        <tr>
            <td>{{ (strlen($pay->payed_on) > 0) ? date('m/d/Y', strtotime($pay->payed_on)): 'N/A' }}</td>
            <td>{{ number_format($pay->amount,2) }}</td>
            <td>{{ $pay->description }}</td>
            <td> <a href="{{ route('jobpayments.showattachment',[$pay->id])}}">{{$pay->attached_file}}</a></td>
            <td>
             @component('admin.jobs.payments.components.deletemodal')
                @slot('id') 
                    {{ $pay->id }}
                @endslot
                 @slot('job_id') 
                    {{ $job->id }}
                @endslot
            @endcomponent
            &nbsp;<a href="{{ route('jobpayments.edit',[$job->id ,$pay->id])}}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i>Edit</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

