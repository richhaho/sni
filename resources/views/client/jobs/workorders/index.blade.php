                     
@if(count($work_orders) > 0 )
<div class="col-xs-12" style="overflow-x: scroll; min-height: 500px">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Number</th>
            <th>Type</th>
           
            <th>Client Name</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Rush</th>
            <th># Attachments</th>
            <th>Service</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($work_orders as $work) 
        <tr>
            <td>{{ $work->number }}</td>
            <td> {{ $wo_types[$work->type] }}</td>
            
            <td> {{ $work->job->client->company_name }}</td>
            <td> {{ (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '' }}</td>
            <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }} </td>
            <td> {{ ($work->is_rush ==1 ) ? 'Yes' : 'No' }}</td>
            <td class="text-center"> {{ ($work->attachments->count()) }}</td>
            <td> {{ $work->service=='self' ? 'Self-Service' : 'Full-Service' }} </td>
            <td>
                <div class="btn-group pull-right dropdown">
                    <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                    </button>
            <ul class="dropdown-menu dropdown-menu-right">
            <li>
                <a class=" " href="{{ route('client.notices.edit',$work->id)}}"><i class="fa fa-pencil"></i> Edit</a>
            </li>
            @if (!in_array($work->status,['cancelled','cancelled charge','completed','cancelled no charge'])) 
                <li>
                    <a class=" " href="{{ route('client.notices.cancel',$work->id)}}"><i class="fa fa-times"></i> Cancel Work Order</a>
                </li>
            @endif
            </ul>
              </div>
            </td>
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
