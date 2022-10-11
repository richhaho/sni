
<div class="row">
    @if(count($linked_jobs) > 0 && Auth::user()->client->is_monitoring_user)
    <div class="col-xs-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Job Name</th>
                    <th class="text-center">Client</th>
                    <th class="text-center">Job Type</th>
                    <th class="text-center">Date Started</th>
                    <th class="text-center">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($linked_jobs as $job)
                <tr>
                    <td> {{ $job->name }}</td>
                    <td> {{ ($job->client) ? $job->client->company_name : "N/A" }}</td>
                    <td> {{ title_case($job->type) }}</td>
                    <td> {{ (strlen($job->started_at) > 0) ? date('m/d/Y', strtotime($job->started_at)): '' }}</td>
                    <td> {{ $job->status=='notice-of-non-payment' ? 'Demand-For-Payment' : title_case($job->status) }}</td>
                    <td>
                        <div class="btn-group pull-right dropup">
                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ route('client.jobs_shared.summary',$job->id)}}"><i class="fa fa-book"></i> View Job Summary</a></li>
                                <li><a href="{{ route('client.jobs_shared.unlink',$job->id)}}"><i class="fa fa-unlink"></i> Unlink</a></li>
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
        <h5>No linked jobs found</h5>
    </div>
    @endif

 </div>


