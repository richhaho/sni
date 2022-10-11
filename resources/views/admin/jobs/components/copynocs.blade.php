<div class="col-xs-12">
    <div class="row">
        <div class="col-md-12 text-center">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>NOC#</th>
                        <th>NOC Recording Date</th>
                        <th>NOC Notes</th>
                        <th>Copy of NOC</th>
                        <th>Expiration Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($xjob->nocs()->get() as $noc)
                    <tr>
                        <td>
                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                                <input name="copy_noc[{{$noc->id}}]" type="checkbox"><span>&nbsp;</span>
                            </label>
                        </div>
                        </td>
                        <td style="word-break: break-all;max-width: 200px;"> {{$noc->noc_number}}</td>
                        <td> {{date('m/d/Y', strtotime($noc->recorded_at))}}</td>
                        <td style="word-break: break-all;max-width: 300px;"> {{$noc->noc_notes}}</td>
                        <td> 
                            @if($noc->copy_noc)
                            <a href="{{route('jobnocs.download', [$xjob->id, $noc->id])}}"><i class="fa fa-download"></i> Download</a>
                            @endif
                        </td>
                        <td> {{date('m/d/Y', strtotime($noc->expired_at))}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>