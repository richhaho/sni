                     
@if(count($work_orders) > 0 )
<div class="col-xs-12">
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
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($work_orders as $work)
        <tr>
            <td> {{ $work->number }}</td>
            <td> {{ $wo_types[$work->type] }}</td>
            
            <td> {{ $work->job->client->company_name }}</td>
            <td> {{ (strlen($work->due_at) > 0) ? date('m/d/Y', strtotime($work->due_at)): '' }}</td>
            <td> {{ (array_key_exists($work->status,$statuses)) ? $statuses[$work->status]: 'None' }}</td>
            <td> {{ ($work->is_rush ==1 ) ? 'Yes' : 'No' }}</td>
            <td class="text-center"> {{ ($work->attachments->count()) }}</td>
            <td>
                <div class="btn-group pull-right dropup">
                    <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                    </button>
            @component('researcher.workorders.components.deletemodal')
                @slot('id') 
                    {{ $work->id }}
                @endslot
                @slot('work_number') 
                    {{ $work->number }}
                @endslot
            @endcomponent
             <li role="separator" class="divider"></li>
                <li>
                    <a class=" " href="{{ route('workorders.edit',$work->id)}}"><i class="fa fa-pencil"></i> Edit</a>
                </li>
                <li>
                    <a class=" " href="{{ route('workorders.createinvoice',$work->id)}}"><i class="fa fa-file-text-o"></i> Create Invoice</a>
                </li>
                @if(in_array($work->type,$available_notices))
                    @if(count($work->attachments->where('type','generated')) > 0)
                    <li class="disabled ">
                        <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                    <li>
                    <li class="">
                        <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                    <li>
                    <li class="">
                        <a  href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                    <li>
                    @else
                    <li class="">
                        <a  href="{{ route('workorders.document',$work->id)}}"><i class="fa  fa-file-pdf-o"></i> Create PDF</a>
                    <li>
                    <li class="disabled">
                        <a  href="{{ route('workorders.deletedocument',$work->id)}}"><i class="fa  fa-trash-o"></i> Delete PDF</a>
                    <li>
                    <li class="disabled ">
                        <a  href="{{ route('workorders.show',$work->id)}}"><i class="fa  fa-eye"></i> View PDF</a>
                    <li>
                    @endif

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
