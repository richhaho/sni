
<div class="row">
    <div class="col-xs-12" style="margin-bottom: 10px">
        <button data-toggle="modal" data-target="#modal-reminder-create" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New Reminder</button>
        @component('client.jobs.reminders.components.createmodal')
            @slot('job_id') 
                {{ $job->id }}
            @endslot
        @endcomponent
    </div>
    @if(count($reminders) > 0 )
    <div class="col-xs-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Emails</th>
                    <th>Note</th>
                    <th>Date On</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reminders as $reminder)
                <tr>
                    <td style="word-break: break-all;max-width: 200px;"> {{$reminder->emails}}</td>
                    <td style="word-break: break-all;max-width: 300px;"> {{$reminder->note}}</td>
                    <td> {{date('m/d/Y', strtotime($reminder->date))}}</td>
                    <td> {{strtoupper($reminder->status)}} {{$reminder->status=='sent' ? 'on '. $reminder->sent_at : ''}}</td>
                    <td>
                        <div class="btn-group pull-right dropup">
                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="#" data-toggle="modal" data-target="#modal-reminder-edit-{{$reminder->id}}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a href="#" data-toggle="modal" data-target="#modal-reminder-delete-{{$reminder->id}}"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                            @component('client.jobs.reminders.components.editmodal')
                                @slot('id') 
                                    {{ $reminder->id }}
                                @endslot
                                @slot('job_id') 
                                    {{ $job->id }}
                                @endslot
                                @slot('emails') 
                                    {{ $reminder->emails }}
                                @endslot
                                @slot('note') 
                                    {{ $reminder->note }}
                                @endslot
                                @slot('date') 
                                    {{ $reminder->date }}
                                @endslot
                            @endcomponent
                            @component('client.jobs.reminders.components.deletemodal')
                                @slot('id') 
                                    {{ $reminder->id }}
                                @endslot
                                @slot('job_id') 
                                    {{ $job->id }}
                                @endslot
                            @endcomponent
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="col-xs-12">
        <h5>No Reminders found</h5>
    </div>
    @endif

 </div>


