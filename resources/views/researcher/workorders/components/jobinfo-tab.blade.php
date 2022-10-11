

<div  class="col-xs-12">
    <h1 class="page-header">{{$job->name}}
        <div class="pull-right">
            <a class="btn btn-success " href="{{ route('jobs.edit',$job->id). '?workorder=' .$work->id}}"> <i class="fa fa-pencil"></i> Edit Job</a>
            
        </div>
    </h1>       
</div>
<div >&nbsp</div>
      <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Job Info
                            </div>
                            <div class="panel-body">
                                <table class="table job-information">
                                    <tbody>
                                        <tr>
                                            <td style="min-width: 200px">Client name:</td>
                                            <td>{{ $job->client->company_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Job name:</td>
                                            <td>{{ $job->name}}</td>
                                        </tr>    
                                        <tr>
                                            <td>Job Address</td>
                                            <td>{!! $job->full_address !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Date Started:</td>
                                            <td>{!! date('m/d/Y', strtotime($job->started_at)) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Folio Number:</td>
                                            <td>{!! $job->folio_number !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Contract Amount:</td>
                                            <td>{!! number_format($job->contract_amount,2) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Interest Rate:</td>
                                            <td>{!! number_format($job->interest_rate,2) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Default Materials:</td>
                                            <td>{!! str_replace(chr(10),'<br>',$job->default_materials) !!}</td>
                                        </tr>  
                                        <tr>
                                            <td>Legal Descriptions:</td>
                                            <td>{!! str_replace(chr(10),'<br>',$job->legal_description) !!}</td>
                                        </tr>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Attachments
                            </div>
                            <div class="panel-body">
                             @foreach ($job->attachments as $attach)
                                @if($loop->first)
                                   <div class="row">
                               @endif
                               <div class="col-md-3 text-center">
                                    <div class="thumbnail">
                                        <a href="{{ route('jobs.showattachment',[$job->id,$attach->id])}}">
                                       <img class="img-responsive" src="{{ route('jobs.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                                       </a>
                                       <div class="caption">
                                         <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                         <p>{{ $attach->description }}</p>
                                         <p>
                                                @if($attach->file_mime =="application/pdf")
                                                <a class="btn btn-xs btn-warning" href="{{route('attachment.print',$attach->id)}}"> <i class="fa fa-print"></i> Print</a>
                                                @endif
                                         </p>
                                       </div>
                                     </div>
                               </div>
                                @if($loop->iteration % 4 == 0 && $loop->last)
                                   </div>
                                @else
                                   @if($loop->iteration % 4 == 0)
                                       </div>
                                       <div class="row">
                                   @else
                                       @if($loop->last)
                                          </div>
                                       @endif
                                   @endif
                               @endif
                           @endforeach   
                                
                            </div>
                        </div>
                    </div>
</div>