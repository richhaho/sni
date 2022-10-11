   <div class="row">
    {!! Form::open(['route' => ['client.notices.addattachment',$work->id],'files' => true,'class'=>'uploadfile']) !!}
    <div class="col-md-4">
        <div class="col-xs-12 form-group filegroup">
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
            <button class="btn btn-success pull-right" type="submit"> <i class="fa fa-floppy-o"></i> Upload</button>
        </div>
    </div>
    {!! Form::close() !!}
    <div class="col-md-8">
        <?php 
        $customer_party=0;$first_gen=0;
        foreach ($work->attachments->where('clientviewable','yes') as $attach){
          if (!$attach->recipient) {
            continue;
          }
          if($attach->type == 'generated' && $attach->recipient->party_type =="customer" ) $customer_party++;
        }
        $attachments = $work->service == 'self' ? $work->attachments : $work->attachments->where('clientviewable','yes');
        ?>
        <div class="row">
        @foreach ($attachments as $attach)
            @if($work->service == 'self')
            <div class="col-md-3 text-center">
              <div class="thumbnail">
                <img class="img-responsive" src="{{ route('client.notices.showthumbnail',[$work->id,$attach->id])}}" alt="{{ $attach->type }}">
                  <div class="caption">
                    <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                    <p>{{ $attach->description }}</p>
                    @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                    <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                    @endif
                      <?php 
                      $attatched_user=\App\User::where('id',$attach->user_id)->first();
                       $client_id=$attatched_user->client_id;
                      ?>
                      
                    <p>
                      @if ($client_id!=0)  
                        @if($attach->type <> 'generated')
                          @component('client.workorders.components.deleteattachmentmodal')
                              @slot('id') 
                                  {{ $attach->id }}
                              @endslot
                              @slot('file_name') 
                                  {{ $attach->original_name }}
                              @endslot
                          @endcomponent
                        @endif
                      @endif

                      @if($work->paid || Auth::user()->client->billing_type=="invoiced")

                       <a href="{{ route('client.notices.downloadattachment',[$work->id,$attach->id])}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Download</a>
                           @if($attach->file_mime =="application/pdf")
                            <a class="btn btn-xs btn-warning" href="{{route('client.attachment.print',$attach->id)}}"> <i class="fa fa-print"></i> Print</a>
                          @endif
                       @else
                       <a href="{{ route('client.invoices.paytodownload',[$work->id,$attach->id])}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Pay & Download</a>
                      @endif
                       
                    </p>
                  </div>
              </div>
            </div>
            @elseif($attach->type <> 'generated' || ($attach->type == 'generated' && $attach->recipient && $attach->recipient->party_type =="customer" ) || ($attach->type == 'generated' && $customer_party == 0 && $first_gen == 0 ) )
            <?php if ($attach->type == 'generated') $first_gen++; ?>
            <div class="col-md-3 text-center">
              <div class="thumbnail">
                <img class="img-responsive" src="{{ route('client.notices.showthumbnail',[$work->id,$attach->id])}}" alt="{{ $attach->type }}">
                  <div class="caption">
                    <h5 style="word-wrap: break-word;">{{ $attach->original_name}}</h5>
                    <p>{{ $attach->description }}</p>
                    @if($attach->type <> 'generated' && $attach->type <> 'mailing-generated')
                    <p>{{ strtoupper(str_replace('-',' ',$attach->type)) }}</p>
                    @endif
                      <?php 
                      $attatched_user=\App\User::where('id',$attach->user_id)->first();
                       $client_id=$attatched_user->client_id;
                      ?>
                      
                    <p>
                      @if ($client_id!=0)  
                        @if($attach->type <> 'generated')
                          @component('client.workorders.components.deleteattachmentmodal')
                              @slot('id') 
                                  {{ $attach->id }}
                              @endslot
                              @slot('file_name') 
                                  {{ $attach->original_name }}
                              @endslot
                          @endcomponent
                        @endif
                      @endif

                      @if($work->paid || Auth::user()->client->billing_type=="invoiced")

                       <a href="{{ route('client.notices.downloadattachment',[$work->id,$attach->id])}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Download</a>
                           @if($attach->file_mime =="application/pdf")
                            <a class="btn btn-xs btn-warning" href="{{route('client.attachment.print',$attach->id)}}"> <i class="fa fa-print"></i> Print</a>
                          @endif
                       @else
                       <a href="{{ route('client.invoices.paytodownload',[$work->id,$attach->id])}}" type="button" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> Pay & Download</a>
                      @endif
                       
                    </p>
                  </div>
              </div>
            </div>
            @endif
             
        @endforeach
        
        </div>
    </div>
</div>