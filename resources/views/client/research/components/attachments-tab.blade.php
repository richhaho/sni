<div class="row">
                {!! Form::open(['route' => ['client.research.addattachment',$job->id],'files' => true,'class'=>'uploadfile']) !!}
                {!! Form::hidden('from', 'research_edit') !!}
                <div class="col-md-4">
                    <div class="col-md-12 form-group filegroup">
                        <label>New File:</label>
                        {!!  Form::file('file','', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Type:</label>
                        {!!  Form::select('type',$attachment_types,'', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Description:</label>
                        {!!  Form::textarea('description','', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-12 form-group">

                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="notify" type="checkbox" ><span>Notify Customer</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 form-group custom_message_group">
                        <label>Notification Message:</label>
                        {!!  Form::textarea('custom_message','Please find the enclosed document that needs to be signed and notarized - Paying attention to any deadline dates, Thank you.', ['class' => 'form-control custom_message', 'rows'=>'4']) !!}
                    </div>
                    <div class="col-md-6 form-group">

                       <div class="checkbox checkbox-slider--b-flat">
                           <label>
                           <input name="clientviewable" type="checkbox" ><span>Hide from client</span>
                           </label>
                       </div>
                    </div>    
                    <div class="col-md-6 form-group ">
                        <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="col-md-8">
                    @foreach ($job->attachments as $attach)
                         @if($loop->first)
                            <div class="row">
                        @endif
                        <div class="col-md-3 text-center">
                             <div class="thumbnail">
                                 <a href="{{ route('client.research.showattachment',[$job->id,$attach->id])}}">
                                <img class="img-responsive" src="{{ route('client.research.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                                </a>
                                <div class="caption">
                                  <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                  <p>{{ $attach->description }}</p>
                                  @if ($attach->clientviewable != 'yes')
                                  <p><strong>Hidden</strong></p>
                                  @endif
                                  @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                                  <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                                  @endif
                                  <p>
                                    @component('client.research.components.deleteattachmentmodal')
                                        @slot('id') 
                                            {{ $attach->id }}
                                        @endslot
                                        @slot('file_name') 
                                            {{ $attach->original_name }}
                                        @endslot
                                        @slot('from') 
                                            {{ 'research_edit' }}
                                        @endslot
                                    @endcomponent
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