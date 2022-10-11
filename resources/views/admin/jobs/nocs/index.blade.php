
<div class="row">
    <div class="col-xs-12" style="margin-bottom: 10px">
        <button data-toggle="modal" data-target="#modal-noc-create" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New NOC</button>
        @component('admin.jobs.nocs.components.createmodal')
            @slot('job_id') 
                {{ $job->id }}
            @endslot
        @endcomponent
    </div>
    @if(count($nocs) > 0 )
    <div class="col-xs-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>NOC#</th>
                    <th>NOC Recording Date</th>
                    <th>NOC Notes</th>
                    <th>Copy of NOC</th>
                    <th>Expiration Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nocs as $noc)
                <tr style="{{$noc->noc_number==$job->noc_number ? 'background-color: #FFE4E1' : ''}}">
                    <td style="word-break: break-all;max-width: 200px;"> {{$noc->noc_number}}</td>
                    <td> {{date('m/d/Y', strtotime($noc->recorded_at))}}</td>
                    <td style="word-break: break-all;max-width: 300px;"> {{$noc->noc_notes}}</td>
                    <td> 
                        @if($noc->copy_noc)
                        <a href="{{route('jobnocs.download', [$job->id, $noc->id])}}"><i class="fa fa-download"></i> Download</a>
                        @endif
                    </td>
                    <td> {{date('m/d/Y', strtotime($noc->expired_at))}}</td>
                    <td>
                        @if($noc->noc_number==$job->noc_number)
                        <span class="text-primary">Current NOC</span>
                        @else
                        <a href="{{route('jobnocs.setcurrent', [$job->id, $noc->id])}}" class="btn btn-warning">Set to Current NOC</a>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group pull-right dropup">
                            <button type="button" class="btn btn-default dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i> Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="#" data-toggle="modal" data-target="#modal-noc-edit-{{$noc->id}}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a href="#" data-toggle="modal" data-target="#modal-noc-delete-{{$noc->id}}"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                            @component('admin.jobs.nocs.components.editmodal')
                                @slot('id') 
                                    {{ $noc->id }}
                                @endslot
                                @slot('job_id') 
                                    {{ $job->id }}
                                @endslot
                                @slot('noc_number') 
                                    {{ $noc->noc_number }}
                                @endslot
                                @slot('recorded_at') 
                                    {{ $noc->recorded_at }}
                                @endslot
                                @slot('noc_notes') 
                                    {{ $noc->noc_notes }}
                                @endslot
                                @slot('copy_noc') 
                                    {{ $noc->copy_noc }}
                                @endslot
                                @slot('expired_at') 
                                    {{ $noc->expired_at }}
                                @endslot
                            @endcomponent
                            @component('admin.jobs.nocs.components.deletemodal')
                                @slot('id') 
                                    {{ $noc->id }}
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
        <h5>No NOCs found</h5>
    </div>
    @endif

 </div>


