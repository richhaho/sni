                     
@if(count($work_orders) > 0 )
<div class="col-xs-12">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Number</th>
            <th>Type</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Rush</th>
            <th>Service</th>
            <th># Attachments</th>
        </tr>
    </thead>
    <tbody>
        @foreach($work_orders as $work)
        <tr>
            <td> {{ $work->number }}</td>
            <td> {{ $wo_types[$work->type] }}</td>
            <td> {{ (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '' }}</td>
            <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }}</td>
            <td> {{ ($work->is_rush ==1 ) ? 'Yes' : 'No' }}</td>
            <td> {{ $work->service=='self' ? 'Self-Service' : 'Full-Service' }} </td>
            <td class="text-center"> {{ ($work->attachments->count()) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@else
<div class="col-xs-12">
    <h5>No Work Orders found</h5>
</div>
@endif
