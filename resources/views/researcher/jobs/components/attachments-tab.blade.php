<div class="row">
                {!! Form::open(['route' => ['jobs.addattachment',$job->id],'files' => true]) !!}
                <div class="col-md-4">
                    <div class="col-xs-12 form-group">
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
                    <div class="col-md-6 form-group">

                        <div class="checkbox checkbox-slider--b-flat">
                            <label>
                            <input name="notify" type="checkbox" ><span>Notify Customer</span>
                            </label>
                        </div>
                     </div> 
                     <div class="col-xs-6 form-group ">
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
                                 <a href="{{ route('jobs.showattachment',[$job->id,$attach->id])}}">
                                <img class="img-responsive" src="{{ route('jobs.showthumbnail',[$job->id,$attach->id])}}" alt="{{ $attach->type }}">
                                </a>
                                <div class="caption">
                                  <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                                  <p>{{ $attach->description }}</p>
                                  <p>
                                    @component('researcher.jobs.components.deleteattachmentmodal')
                                        @slot('id') 
                                            {{ $attach->id }}
                                        @endslot
                                        @slot('file_name') 
                                            {{ $attach->original_name }}
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