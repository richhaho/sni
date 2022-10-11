<div class="row">
                {!! Form::open(['route' => ['client.jobs.addattachment',$job->id],'files' => true,'class'=>'uploadfile']) !!}
                <div class="col-md-4">
                    <div class="col-xs-12 form-group  filegroup">
                        <label>New File:</label>
                        {!!  Form::file('file','', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-xs-12 form-group">
                        <label>Type:</label>
                        {!!  Form::select('type',$attachment_types,'', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-xs-12 form-group">
                        <label>Description:</label>
                        {!!  Form::textarea('description','', ['class' => 'form-control']) !!}
                    </div>
                     <div class="col-xs-12 form-group ">
                        <button class="btn btn-success pull-right attachment_save" type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="col-md-8">
                    @foreach ($job->attachments->where('clientviewable','yes') as $attach)
                         @if($loop->first)
                            <div class="row">
                        @endif
                        <div class="col-md-3 text-center">
                             <div class="thumbnail">
                                 <a href="{{ route('client.jobs.showattachment',[$job->id,$attach->id])}}">
                                <img class="img-responsive" src="{{ route('client.jobs.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                                </a>
                                <div class="caption">
                                  <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                  <p>{{ $attach->description }}</p>
                                  @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                                  <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                                  @endif
                                  <p>
<?php 
 
 $attatched_user=\App\User::where('id',$attach->user_id)->first();
 if ($attatched_user) {$client_id=$attatched_user->client_id;}else{$client_id=0;}
?>                                  
                                    @if ($client_id!=0)
                                    @component('client.jobs.components.deleteattachmentmodal')
                                        @slot('id') 
                                            {{ $attach->id }}
                                        @endslot
                                        @slot('file_name') 
                                            {{ $attach->original_name }}
                                        @endslot
                                    @endcomponent
                                    @endif
                                    <a href="{{ route('client.jobs.showattachment',[$job->id,$attach->id])}}" class="btn btn-xs btn-warning">
                                        <i class="fa fa-success"></i> Download</i>
                                    </a>
                                    @if($attach->file_mime =="application/pdf")
                                    <a class="btn btn-xs btn-primary" href="{{route('client.attachment.print',$attach->id)}}"> <i class="fa fa-print"></i> Print</a>
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